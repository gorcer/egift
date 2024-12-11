<?php

namespace App\Services;

use App\Models\Certificate;

class TelegramService
{
    /**
     * Send a notification to the Telegram admin.
     *
     * @param \App\Models\Certificate $certificate
     * @return void
     */
    public function sendAuthToAdmin(Certificate $certificate)
    {
        $adminChatId = config('telegram.bots.mybot.admin_chat_id');
        $telegram = new \Telegram\Bot\Api();

        $message = "Клиент активировал сертификат:\n" .
            "Код сертификата: {$certificate->code}\n" .
            "Email пользователя: {$certificate->auth_info['user_email']} \n\n" .
            "Купите для пользователя продукт на странице {$certificate->product->provider_url}.\n" .
            "Когда исполнитель попросит код, нажмите кнопку ЗАПРОСИТЬ КОД.";

        // Добавление кнопки
        $inlineKeyboard = [
            [
                [
                    'text' => 'ЗАПРОСИТЬ КОД',
                    'callback_data' => "request_code:{$certificate->id}"
                ]
            ]
        ];

        $telegram->sendMessage([
            'chat_id' => $adminChatId,
            'text' => $message,
            'reply_markup' => json_encode(['inline_keyboard' => $inlineKeyboard]),
        ]);
    }

    public function handleRequest(array $update)
    {
        // Проверяем, является ли запрос callback-запросом
        if (isset($update['callback_query'])) {
            $callbackQuery = $update['callback_query'];
            $data = $callbackQuery['data'];
            $chatId = $callbackQuery['message']['chat']['id'];
            $messageId = $callbackQuery['message']['message_id'];

            // Обрабатываем данные callback
            if (str_starts_with($data, 'request_code:')) {
                $certificateId = explode(':', $data)[1];

                // Найти сертификат и обновить статус
                $certificate = Certificate::find($certificateId);
                if ($certificate) {
                    $certificate->update(['status' => Certificate::STATUS_CODE_AWAITING]);

                    // Уведомление администратора
                    $telegram = new \Telegram\Bot\Api();
                    $telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "Статус сертификата с кодом {$certificate->code} обновлен на 3.",
                    ]);

                    // Удаляем клавиатуру, чтобы кнопка больше не была активной
                    $telegram->editMessageReplyMarkup([
                        'chat_id' => $chatId,
                        'message_id' => $messageId,
                        'reply_markup' => json_encode(['inline_keyboard' => []]),
                    ]);
                }
            }

            // Обрабатываем callback "task_done"
            if (str_starts_with($data, 'task_done:')) {
                $certificateId = explode(':', $data)[1];

                // Найти сертификат и обновить статус
                $certificate = Certificate::find($certificateId);
                if ($certificate) {
                    $certificate->update(['status' => Certificate::STATUS_FINISHED]);

                    // Уведомляем администратора
                    $telegram = new \Telegram\Bot\Api();
                    $telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "Статус сертификата с кодом {$certificate->code} обновлен на 'Выполнено' (4).",
                    ]);

                    // Удаляем клавиатуру, чтобы кнопка больше не была активной
                    $telegram->editMessageReplyMarkup([
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
        $adminChatId = config('telegram.bots.mybot.admin_chat_id');
        $telegram = new \Telegram\Bot\Api();

        $message = "Клиент отправил код из письма:\n" .
            "Код сертификата: {$certificate->code}\n" .
            "Email пользователя: {$certificate->auth_info['user_email']}\n" .
            "Код из письма: {$mailCode} \n\n" .
            "Передайте код исполнителю, дождитесь когда он отчитается о выполнении работы и нажмите кнопку ЗАДАНИЕ ВЫПОЛНЕНО.";

        // Добавляем кнопку "ЗАДАНИЕ ВЫПОЛНЕНО"
        $inlineKeyboard = [
            [
                [
                    'text' => 'ЗАДАНИЕ ВЫПОЛНЕНО',
                    'callback_data' => "task_done:{$certificate->id}"
                ]
            ]
        ];

        $telegram->sendMessage([
            'chat_id' => $adminChatId,
            'text' => $message,
            'reply_markup' => json_encode(['inline_keyboard' => $inlineKeyboard]),
        ]);
    }



}
