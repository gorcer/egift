<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <style>
        @font-face {
            font-family: 'Roboto Mono';
            src: url('https://fonts.gstatic.com/s/robotomono/v11/L0x5DF4xlVMF-BfR8bXMIjC4mVfeWg.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }



        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 297mm; /* Высота A4 */
            width: 210mm; /* Ширина A4 */
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
            border-bottom: 2px dashed #000; /* Разделяющая пунктирная линия */
        }

        h1 {
            font-size: 36px;
            font-weight: bold;
            margin: 0;
        }

        .qr-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 0 10mm; /* Отступы слева и справа */
            box-sizing: border-box;
        }

        .qr-code {
            width: 300px; /* Размеры QR-кода */
            height: 300px;
        }

        .qr-text {
            font-size: 14px;
            text-align: left;
            flex: 1;
            margin-left: 10mm;
        }

        .bottom-text {
            font-size: 16px;
            text-align: left;
            padding: 0 10mm;
            line-height: 1.5;
        }

        strong {
            font-size: 20px; /* Размер кода активации */
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<!-- Первая часть -->
<div class="section">
    <h1>{{ $title }}</h1>
</div>

<!-- Вторая часть -->
<div class="section">
    <div class="qr-section">
        <img class="qr-code" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(300)->generate($qr_code_url)) !!}" alt="QR Code">
        <div class="qr-text">
            <p>Данный сертификат дает право на получение:</p>
            <p><strong>{{ $description }}</strong></p>
            Код активации: <strong>{{ $activation_code }}</strong>
        </div>
    </div>
</div>

<!-- Третья часть -->
<div class="section">
    <div class="bottom-text">
        <p>Наведите камеру на QR-код, чтобы отсканировать его.</p>
        <p>Перейдите по ссылке и введите код активации.</p>
    </div>
</div>
</body>
</html>
