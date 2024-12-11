@extends('layouts.app')

@section('title', 'egift.gorcer.com - Успех')

@section('content')
    <div class="bg-white p-6 rounded shadow-md w-full max-w-md mx-auto">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-center text-red-700 mb-3">Успех!</h1>
            <div class="flex-col justify-center items-center">
                <div class="loader mb-4">Работа успешно выполнена, проверяйте!</div>
                <div>
                    Остались вопросы? <br/>
                    Напишите нам <a target="_blank" class="underline text-blue-900" href="https://t.me/Gorcer">@gorcer</a>
                </div>
            </div>
        </div>
    </div>
@endsection
