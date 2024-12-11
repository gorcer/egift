@extends('layouts.app')

@section('title', 'egift.gorcer.com - Ожидание обработки')

@section('content')

    @php
        use App\Models\Certificate;
    @endphp

    <div class="bg-white p-6 rounded shadow-md w-full max-w-md mx-auto">
        <div class="text-center">
            <p class="text-lg mb-4">Ожидайте, ваш заказ обрабатывается...</p>
            <div class="flex justify-center items-center">
                <div class="loader mb-4"></div>
            </div>
        </div>
    </div>

    <script>
        async function checkStatus() {
            try {
                const response = await fetch('{{ route('cert.status', $certificate->code) }}');
                const data = await response.json();

                if (data.status ===  {{Certificate::STATUS_CODE_AWAITING}}) {
                    window.location.href = '{{ route('cert.mail_code', $certificate->code) }}';
                } else if(data.status ===  {{Certificate::STATUS_FINISHED}}) {
                    window.location.href = '{{ route('cert.done', $certificate->code) }}';
                }
                else {
                    setTimeout(checkStatus, 3000); // Повторная проверка каждые 3 секунды
                }
            } catch (error) {
                console.error('Ошибка при проверке статуса:', error);
                setTimeout(checkStatus, 5000); // Повторная проверка через 5 секунд в случае ошибки
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            checkStatus();
        });
    </script>

    <style>
        .loader {
            border: 4px solid #f3f3f3;
            border-radius: 50%;
            border-top: 4px solid #3498db;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endsection
