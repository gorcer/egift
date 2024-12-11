@extends('layouts.app')

@section('title', 'egift.gorcer.com - Электроподарочная')

@section('content')
    <div class="bg-white p-6 rounded shadow-md w-full max-w-md mx-auto">
        <h1 class="text-2xl font-bold text-center text-red-700 mb-3">Активация сертификата</h1>

        {{-- Вывод ошибок --}}
        @if ($errors->any())
            <div class="bg-red-100 text-red-600 p-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Вывод сообщения об успешной активации --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-600 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Форма ввода кода активации --}}
        <form action="{{ route('cert.verify', $certificate->code) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="activation_code" class="block text-sm font-medium text-gray-700 mb-2">Введите код активации</label>
                <input type="text" name="activation_code" id="activation_code"
                       class="block w-full border border-gray-400 rounded-lg shadow-sm p-3 text-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       placeholder="Например, ABC123" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">Активировать</button>
        </form>
    </div>
@endsection
