@extends('base')

@section('body')
    <div class="w-full h-full flex flex-col justify-center items-center">
        <form
            hx-boost="true"
            class="flex flex-col rounded-2xl shadow-2xl p-8 gap-6 bg-white"
            method="POST"
            action="{{ route('login_handle') }}"
        >
            @csrf

            <div class="flex flex-col">
                <label for="email">E-mail<x-RequiredStar /></label>
                <x-auth::input id="email" name="email" placeholder="jan@novak.cz" required value="{{ old('email') }}"/>
            </div>

            <div class="flex flex-col">
                <label for="password">Heslo<x-RequiredStar /></label>
                <x-auth::input id="password" name="password" type="password" placeholder="******" required />
            </div>

            <div class="flex flex-row justify-end">
                <button class="px-4 py-2 rounded-2xl bg-primary shadow hover:brightness-90 transition-all">Přihlásit se</button>
            </div>
        </form>

        @if ($errors->has('email'))
            <div class="absolute bg-white w-full max-w-sm rounded-md flex flex-row p-2 bottom-0 mb-3 shadow-2xl">
                <div
                    class="border w-full h-full p-2 rounded-md flex flex-row items-center gap-5 hover:cursor-pointer"
                    onclick="this.parentElement.remove()"
                >
                    <span class="rounded-full bg-red-600 flex justify-center items-center min-w-[2em] min-h-[2em] text-red-50">x</span>
                    {{ $errors->first('email') }}
                </div>
            </div>
        @endif
    </div>
@endsection
