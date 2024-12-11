@extends('layouts.app')

@section('title', 'egift.gorcer.com - Ввод кода из письма')

@section('content')
    <div class="bg-white p-6 rounded shadow-md w-full max-w-md mx-auto">

            <h1 class="text-2xl font-bold text-center text-red-700 mb-3">Введите код из письма</h1>
        <div class="mb-2">
            На адрес {{$certificate->auth_info['user_email']}} должно прийти письмо с кодом, укажите этот код в поле ниже.
        </div>
            <form action="{{ route('cert.submit_mail_code', $certificate->code) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <input type="text" name="mail_code" id="mail_code"
                           class="block w-full border border-gray-400 rounded-lg shadow-sm p-3 text-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                           placeholder="Введите код" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">Подтвердить</button>
            </form>


    </div>
@endsection
