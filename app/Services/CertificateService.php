<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Product;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateService
{
    /**
     * Create a certificate based on the product alias and email.
     *
     * @param string $productAlias
     * @param string $email
     * @return Certificate|null
     * @throws \Exception
     */
    public function createCertificate(string $productAlias, string $email): ?Certificate
    {
        // Find the product by alias
        $product = Product::where('alias', $productAlias)->first();

        if (!$product) {
            throw new \Exception('Product with alias "' . $productAlias . '" not found.');
        }

        // Generate the fields for the certificate
        $code = Str::uuid()->toString();
        $activation_code = Str::random(5);
        $price = $product->price;

        // Create the certificate record
        $certificate = Certificate::create([
            'product_alias' => $productAlias,
            'email' => $email,
            'code' => $code,
            'activation_code' => strtoupper($activation_code),
            'price' => $price,
        ]);

        // Generate PDF
        $pdf = $this->generatePdf($certificate);

        // Send email with the PDF
        $this->sendEmailWithPdf($email, $certificate, $pdf);

        return $certificate;
    }

    /**
     * Generate a PDF for the certificate.
     *
     * @param Certificate $certificate
     * @return string
     */
    protected function generatePdf(Certificate $certificate): string
    {
        $data = [
            'title' => 'Your Certificate',
            "description" => 'Description',
            'qr_code_url' => url('/cert/' . $certificate->code),
            'activation_code' => $certificate->activation_code,
        ];

        $pdf = Pdf::loadView('certificates.template', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->output();
    }

    /**
     * Send an email with the generated PDF.
     *
     * @param string $email
     * @param Certificate $certificate
     * @param string $pdfContent
     * @return void
     */
    protected function sendEmailWithPdf(string $email, Certificate $certificate, string $pdfContent): void
    {
        Mail::send([], [], function (Message $message) use ($email, $certificate, $pdfContent) {
            $message->to($email)
                ->subject('Your Certificate')
                ->html('<p>Dear customer,</p><p>Your certificate is attached.</p>')
                ->attachData($pdfContent, 'certificate.pdf', [
                    'mime' => 'application/pdf',
                ]);
        });
    }
}
