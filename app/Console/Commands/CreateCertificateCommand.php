<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CertificateService;

class CreateCertificateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificate:create
                            {product : The product alias for the certificate}
                            {email : The email associated with the certificate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new certificate with a product alias and email';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $productAlias = $this->argument('product');
        $email = $this->argument('email');

        // Используем CertificateService для создания сертификата
        $certificateService = new CertificateService();

        try {
            $certificate = $certificateService->createCertificate($productAlias, $email);

            $this->info('Certificate created successfully!');
            $this->info('Certificate ID: ' . $certificate->id);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
