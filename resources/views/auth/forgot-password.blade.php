@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-md">

            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Mot de passe oublié</h2>

            <p class="text-sm text-gray-600 mb-4 text-center">
                Entrez votre adresse email pour recevoir un lien de réinitialisation.
            </p>

            @if (session('status'))
                <div class="mb-4 bg-green-100 text-green-700 p-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 text-red-600 p-3 rounded">
                    <ul class="list-disc ml-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm mb-1">Email</label>
                    <input type="email" name="email" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    Envoyer le lien
                </button>
            </form>

            <p class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-blue-600 text-sm hover:underline">
                    Retour à la connexion
                </a>
            </p>

        </div>
    </div>
@endsection
