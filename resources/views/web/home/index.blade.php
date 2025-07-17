@extends('layouts.web')
@section('content')
    <div class="text-center px-6">
        <h1 class="text-4xl md:text-5xl font-bold text-yellow-400 mb-4">Bem-vindo à BetMaster</h1>
        <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
            A BetMaster é a sua plataforma de apostas esportivas e jogos online. Cadastre-se agora e comece a ganhar prêmios
            incríveis!
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('index.auth', ['mode' => 'login']) }}" style="color: black; text-decoration: none" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold transition">Entrar</a>
            <a href="{{ route('index.auth', ['mode' => 'registrar']) }}"
                style="color: black; text-decoration: none" 
                class="px-6 py-3 bg-yellow-400 text-black hover:bg-yellow-500 rounded-lg font-semibold transition">Registrar-se</a>
        </div>
    </div>
@endsection
