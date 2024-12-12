<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetTelegramWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the Telegram webhook';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $botToken = config('telegram.bots.mybot.token');

        $url = config('app.url').route('telegram.webhook',[], false);
        echo 'Try to set the Telegram webhook ' . $url.PHP_EOL;
        $response = Http::post("https://api.telegram.org/bot{$botToken}/setWebhook", [
            'url' => $url,
        ]);

        if ($response->successful()) {
            $this->info('Webhook set successfully to '.route('telegram.webhook'));
        } else {
            $this->error('Failed to set webhook.');
            $this->error($response->body());
        }

        return 0;
    }
}
