@extends('layouts.app')

@section('title', 'Registro de paciente')

@section('content')
    <div class="card" style="max-width: 520px; margin: 2rem auto;">
        <h1>Registro de paciente</h1>
        <p class="muted">Cree su cuenta para solicitar citas médicas.</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <label for="first_name">Nombre</label>
            <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required>

            <label for="last_name">Apellido</label>
            <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required>

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>

            <label for="document_number">Documento</label>
            <input id="document_number" type="text" name="document_number" value="{{ old('document_number') }}">

            <label for="phone">Teléfono</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}">

            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required>

            <label for="password_confirmation">Confirmar contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>

            <button type="submit" class="btn">Registrarse</button>
        </form>

        <p style="margin-top:1rem;">
            <a href="{{ route('login') }}">Ya tengo cuenta</a>
        </p>
    </div>
@endsection
