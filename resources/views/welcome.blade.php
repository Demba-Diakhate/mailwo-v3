@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<header class="w-full bg-white shadow">
    <div x-data="{ open: false }" class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

        {{-- Logo Mailwo --}}
        <a href="/" class="text-3xl font-extrabold text-blue-600">
            Mailwo
        </a>

        {{-- Desktop Buttons --}}
        <div class="hidden md:flex items-center gap-4">

            {{-- Campagne --}}
            <a href="{{ route('import.create') }}"
               class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg shadow hover:bg-blue-700 transition">
                Lancer une campagne
            </a>

            {{-- Connexion --}}
            <a href="{{ route('login') }}"
               class="px-4 py-2 text-blue-600 font-medium border border-blue-200 rounded-lg hover:bg-blue-50 transition">
               Connexion
            </a>
        </div>

        {{-- Mobile Menu Button --}}
        <button @click="open = !open"
                class="md:hidden text-gray-700 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-7 w-7"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

    </div>

    {{-- Mobile Dropdown --}}
    <div x-cloak
         x-show="open"
         x-transition
         class="md:hidden bg-white border-t px-6 pb-4">

        {{-- Lancer une campagne --}}
        <a href="{{ route('import.create') }}"
           class="block w-full text-left py-2 mt-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700">
            Lancer une campagne
        </a>

        {{-- Connexion --}}
        <a href="{{ route('login') }}"
           class="block w-full text-left py-2 mt-3 text-blue-600 font-medium border border-blue-200 rounded-lg hover:bg-blue-50">
            Connexion
        </a>

    </div>
</header>



{{-- MAIN CONTENT --}}
<div class="mt-28 bg-gray-100 flex flex-col justify-center items-center px-6 py-10">

    {{-- HERO --}}
    <div class="text-center max-w-3xl">
        <h1 class="text-5xl font-extrabold text-gray-800 mb-4">
            Bienvenue sur <span class="text-blue-600">Mailwo</span>
        </h1>
        <p class="text-gray-600 text-lg leading-relaxed">
            La plateforme simple et puissante pour envoyer vos campagnes d’emailing,
            gérer vos contacts et analyser vos performances.
        </p>
    </div>

    <div class="mt-10">
        {{-- Inscription --}}
        <a href="{{ route('register') }}"
            class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg shadow hover:bg-blue-700 transition">
            Inscription
        </a>
    </div>

</div>

@endsection
