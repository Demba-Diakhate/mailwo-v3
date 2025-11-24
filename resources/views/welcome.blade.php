@extends('layouts.app')

@section('content')
    <div class="flex flex-col justify-center items-center">
        <h1 class="text-center font-bold text-5xl py-10">Bienvenue sur Maliwo</h1>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="px-3 py-1 bg-red-700 text-white rounded cursor-pointer">
                Déconnexion
            </button>
        </form>
        <a href="import/create" class="px-3 py-1 mt-5 bg-green-700 text-white rounded cursor-pointer">
            Importer des contacts
        </a>
    </div>
    
@endsection