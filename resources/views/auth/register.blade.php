@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-md">

            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Créer un compte</h2>

            @if ($errors->any())
                <div class="mb-4 bg-red-100 text-red-600 p-3 rounded">
                    <ul class="list-disc ml-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm mb-1">Nom complet</label>
                    <input type="text" name="name" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm mb-1">Email</label>
                    <input type="email" name="email" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm mb-1">Mot de passe</label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm mb-1">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    S'inscrire
                </button>
            </form>

            <p class="mt-4 text-center text-sm">
                Déjà un compte ?
                <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:underline">Se connecter</a>
            </p>

        </div>
    </div>
@endsection
