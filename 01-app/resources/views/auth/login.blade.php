@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
    <div class="card" style="max-width: 420px; margin: 2rem auto;">
        <h1>Iniciar sesión</h1>
        <p class="muted">Acceda a WhiteAssistance con su cuenta.</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required>

            <label style="display:flex; align-items:center; gap:.5rem; font-weight:400;">
                <input type="checkbox" name="remember" style="width:auto; margin:0;">
                Recordarme
            </label>

            <button type="submit" class="btn">Entrar</button>
        </form>

        <p style="margin-top:1rem;">
            <a href="{{ route('register') }}">Registrarse como paciente</a>
        </p>
    </div>
@endsection
