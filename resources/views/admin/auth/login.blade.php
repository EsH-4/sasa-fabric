<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Admin · Sasa Fabric</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="brand"><img src="{{ asset('images/logo.png') }}" alt="Sasa Fabric"></div>
        <h1>Admin Sasa Fabric</h1>
        <p class="muted">Masuk untuk mengelola katalog kain dan konten website.</p>
        @if ($errors->any())
            <p class="error">{{ $errors->first() }}</p>
        @endif
        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf
            <label class="f"><span>Email</span><input type="email" name="email" value="{{ old('email') }}" required autofocus></label>
            <label class="f"><span>Kata Sandi</span><input type="password" name="password" required></label>
            <label class="f checkbox-f">
                <input type="checkbox" name="remember" value="1">
                <span>Ingat Saya</span>
            </label>
            <button class="btn pri" type="submit">Masuk ke Panel</button>
        </form>
    </div>
</div>
</body>
</html>
