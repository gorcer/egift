<?php

namespace App\Services;

use App\Models\Certificate;
use Telegram\Bot\Api;

class TelegramService
{
    protected $telegram;
    protected $adminChatId;

    /**
     * TelegramService constructor.
     */
    public function __construct()
    {
        $token = config('telegram.bots.mybot.token');
        $this->telegram = new Api($token);
        $this->adminChatId = config('telegram.bots.mybot.admin_chat_id');
    }

    /**
     * Send a notification to the Telegram admin.
     *
     * @param \App\Models\Certificate $certificate
     * @return void
     */
    public function sendAuthToAdmin(Certificate $certificate)
    {
        $message = "Клиент активировал сертификат:\n" .
            "Код сертификата: {$certificate->code}\n" .
            "Email пользователя: {$certificate->auth_info['user_email']} \n\n" .
            "Купите для пользователя продукт на странице {$certificate->product->provider_url}.\n" .
            "Когда исполнитель попросит код, нажмите кнопку ЗАПРОСИТЬ КОД.";

        $inlineKeyboard = [
            [
                [
                    'text' => 'ЗАПРОСИТЬ КОД',
                    'callback_data' => "request_code:{$certificate->id}"
                ]
            ]
        ];

        $this->telegram->sendMessage([
            'chat_id' => $this->adminChatId,
            'text' => $message,
            'reply_markup' => json_encode(['inline_keyboard' => $inlineKeyboard]),
        ]);
    }

    /**
     * Handle incoming Telegram requests.
     *
     * @param array $update
     * @return void
     */
    public function handleRequest(array $update)
    {
        if (isset($update['callback_query'])) {
            $callbackQuery = $update['callback_query'];
            $data = $callbackQuery['data'];
            $chatId = $callbackQuery['message']['chat']['id'];
            $messageId = $callbackQuery['message']['message_id'];

            if (str_starts_with($data, 'request_code:')) {
                $certificateId = explode(':', $data)[1];
                $certificate = Certificate::find($certificateId);
                if ($certificate) {
                    $certificate->update(['status' => Certificate::STATUS_CODE_AWAITING]);

                    $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "Статус сертификата с кодом {$certificate->code} обновлен на 3.",
                    ]);

                    $this->telegram->editMessageReplyMarkup([
                        'chat_id' => $chatId,
                        'message_id' => $messageId,
                        'reply_markup' => json_encode(['inline_keyboard' => []]),
                    ]);
                }
            }

            if (str_starts_with($data, 'task_done:')) {
                $certificateId = explode(':', $data)[1];
                $certificate = Certificate::find($certificateId);
                if ($certificate) {
                    $certificate->update(['status' => Certificate::STATUS_FINISHED]);

                    $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "Статус сертификата с кодом {$certificate->code} обновлен на 'Выполнено' (4).",
                    ]);

                    $this->telegram->editMessageReplyMarkup([
                        'chat_id' => $chatId,
                        'message_id' => $messageId,
                        'reply_markup' => json_encode(['inline_keyboard' => []]),
                    ]);
                }
            }
        }
    }

    /**
     * Send the mail code to the Telegram admin.
     *
     * @param \App\Models\Certificate $certificate
     * @param string $mailCode
     * @return void
     */
    public function sendMailCodeToAdmin(Certificate $certificate, string $mailCode)
    {
        $message = "Клиент отправил код из письма:\n" .
            "Код сертификата: {$certificate->code}\n" .
            "Email пользователя: {$certificate->auth_info['user_email']}\n" .
            "Код из письма: {$mailCode} \n\n" .
            "Передайте код исполнителю, дождитесь когда он отчитается о выполнении работы и нажмите кнопку ЗАДАНИЕ ВЫПОЛНЕНО.";

        $inlineKeyboard = [
            [
                [
                    'text' => 'ЗАДАНИЕ ВЫПОЛНЕНО',
                    'callback_data' => "task_done:{$certificate->id}"
                ]
            ]
        ];

        $this->telegram->sendMessage([
            'chat_id' => $this->adminChatId,
            'text' => $message,
            'reply_markup' => json_encode(['inline_keyboard' => $inlineKeyboard]),
        ]);
    }
}
