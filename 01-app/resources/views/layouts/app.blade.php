<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'WhiteAssistance')</title>
    <style>
        :root { --primary: #0f766e; --bg: #f8fafc; --card: #ffffff; --text: #0f172a; --muted: #64748b; --border: #e2e8f0; --danger: #b91c1c; --success: #15803d; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, -apple-system, sans-serif; background: var(--bg); color: var(--text); }
        a { color: var(--primary); text-decoration: none; }
        .container { max-width: 960px; margin: 0 auto; padding: 1.5rem; }
        .nav { background: var(--card); border-bottom: 1px solid var(--border); padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; }
        .nav-brand { font-weight: 700; color: var(--text); }
        .nav-links { display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem; }
        .btn { display: inline-block; background: var(--primary); color: #fff; border: 0; border-radius: 8px; padding: .6rem 1rem; cursor: pointer; font: inherit; }
        .btn-secondary { background: #334155; }
        .btn-danger { background: var(--danger); }
        .alert { padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .alert-success { background: #dcfce7; color: var(--success); }
        .alert-error { background: #fee2e2; color: var(--danger); }
        .muted { color: var(--muted); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: .75rem; border-bottom: 1px solid var(--border); text-align: left; }
        label { display: block; margin-bottom: .35rem; font-weight: 600; }
        input, select, textarea { width: 100%; padding: .6rem .75rem; border: 1px solid var(--border); border-radius: 8px; font: inherit; margin-bottom: 1rem; }
        .grid { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
        .badge { display: inline-block; padding: .2rem .55rem; border-radius: 999px; background: #ecfeff; color: #155e75; font-size: .85rem; }
        .actions { display: flex; gap: .5rem; flex-wrap: wrap; }
    </style>
</head>
<body>
    @auth
        <nav class="nav">
            <div class="nav-brand">WhiteAssistance</div>
            <div class="nav-links">
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}">Inicio</a>
                @elseif(auth()->user()->hasRole('specialist'))
                    <a href="{{ route('specialist.dashboard') }}">Inicio</a>
                @else
                    <a href="{{ route('patient.dashboard') }}">Inicio</a>
                @endif
                <a href="{{ route('appointments.index') }}">Citas</a>
                @can('create', App\Modules\Appointments\Infrastructure\Models\Appointment::class)
                    <a href="{{ route('appointments.create') }}">Nueva cita</a>
                @endcan
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Salir</button>
                </form>
            </div>
        </nav>
    @endauth

    <main class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
