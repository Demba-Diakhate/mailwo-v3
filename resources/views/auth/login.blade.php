@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-md">

            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Connexion</h2>

            @if ($errors->any())
                <div class="mb-4 bg-red-100 text-red-600 p-3 rounded">
                    <ul class="list-disc ml-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm mb-1">Email</label>
                    <input type="email" name="email" required autofocus
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm mb-1">Mot de passe</label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>

                <div class="flex justify-between items-center mb-4">
                    <label class="flex items-center text-sm">
                        <input type="checkbox" name="remember" class="mr-2">
                        Se souvenir de moi
                    </label>

                    <a href="{{ route('password.request') }}" class="text-blue-600 text-sm hover:underline">
                        Mot de passe oublié ?
                    </a>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    Se connecter
                </button>
            </form>

            <p class="mt-4 text-center text-sm">
                Pas de compte ?
                <a href="{{ route('register') }}" class="text-blue-600 font-medium hover:underline">Créer un compte</a>
            </p>

        </div>
    </div>
@endsection
