<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/fonts/DejaVuSans.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            /*width: 210mm;*/
            /*height: 267mm; !* Формат A4 *!*/
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 5mm; /* Уменьшенные отступы */
            box-sizing: border-box;
        }

        .section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .section:not(:last-child) {
            border-bottom: 1px dashed #000; /* Уменьшенная толщина пунктирной линии */
        }

        h1 {
            font-size: 18px; /* Уменьшенный размер заголовка */
            margin: 0;
        }

        p {
            font-size: 12px; /* Уменьшенный размер текста */
            margin: 4px 0;
            line-height: 1.3;
        }

        .qr-section {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5mm; /* Уменьшенный промежуток */
        }

        .qr-code {
            width: 80px; /* Уменьшенные размеры QR-кода */
            height: 80px;
        }

        .qr-text {
            font-size: 12px;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Первая часть -->
    <div class="section">
        <h1>{{ $title }}</h1>
        <p>{{ $description }}</p>
    </div>

    <!-- Вторая часть -->
    <div class="section">
        <div class="qr-section">
            <img class="qr-code" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(80)->generate($qr_code_url)) !!}" alt="QR Code">
            <div class="qr-text">
                <p>Наведите камеру на QR-код, чтобы отсканировать его.</p>
                <p>Перейдите по ссылке и введите код активации:</p>
                <p><strong>{{ $activation_code }}</strong></p>
            </div>
        </div>
    </div>

    <!-- Третья часть -->
    <div class="section"></div>
</div>
</body>
</html>
