<?php

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/cert/{code}', [CertificateController::class, 'show'])->name('cert.show');
Route::post('/cert/{code}', [CertificateController::class, 'verify'])->name('cert.verify');
Route::get('/cert/{code}/email', [CertificateController::class, 'enterEmail'])->name('cert.enterEmail');
Route::post('/cert/{code}/email', [CertificateController::class, 'saveEmail'])->name('cert.saveEmail');
Route::get('/cert/{code}/wait', [CertificateController::class, 'waitForStatus'])->name('cert.wait');
Route::get('/cert/{code}/status', [CertificateController::class, 'checkStatus'])->name('cert.status');
Route::get('/cert/{code}/mail-code', [CertificateController::class, 'enterMailCode'])->name('cert.mail_code');
Route::post('/cert/{code}/mail-code', [CertificateController::class, 'submitMailCode'])->name('cert.submit_mail_code');
Route::get('/cert/{code}/done', [CertificateController::class, 'done'])->name('cert.done');

Route::get('/test-pdf', function () {
    $pdf = Pdf::loadHTML('<h1>Test PDF</h1>');
    return $pdf->download('test.pdf');
});
