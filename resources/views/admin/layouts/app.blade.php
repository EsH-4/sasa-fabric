<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') · Sasa Fabric</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="layout">
    <aside class="side">
        <div class="side-head">
            <div class="brand"><img src="{{ asset('images/logo.png') }}" alt="Sasa Fabric"></div>
            <button type="button" class="menu-btn" id="menuToggleBtn" aria-label="Buka menu" aria-expanded="false">☰</button>
        </div>
        <nav id="sideNav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Ringkasan</a>
            <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">Edit halaman</a>
            <a href="{{ route('admin.gallery.index') }}" class="{{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">Galeri foto</a>
            <button type="button" class="link" id="triggerViewSiteBtn">Lihat situs</button>
            <button type="button" class="link link-danger" id="triggerLogoutBtn">Keluar</button>
        </nav>
    </aside>
    <main class="main">
        <div class="top">
            <h1>@yield('title', 'Admin')</h1>
            <span class="muted">{{ auth()->user()->name }}</span>
        </div>
        @if (session('status'))
            <div class="alert">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
</div>

<!-- Modal Konfirmasi Lihat Situs -->
<div id="viewSiteModal" class="modal-backdrop">
    <div class="modal-box modal-box-sm" role="dialog" aria-modal="true">
        <h3 class="modal-title">Buka Halaman Utama?</h3>
        <p class="muted modal-text">
            Anda akan diarahkan ke halaman depan katalog publik Sasa Fabric.
        </p>
        <div class="modal-actions">
            <button type="button" class="btn" id="cancelViewSiteBtn">Batal</button>
            <a href="{{ route('home') }}" target="_blank" class="btn pri modal-btn-confirm" id="confirmViewSiteBtn">Buka Situs</a>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Logout -->
<div id="logoutModal" class="modal-backdrop">
    <div class="modal-box modal-box-sm" role="dialog" aria-modal="true">
        <h3 class="modal-title">Keluar dari Admin?</h3>
        <p class="muted modal-text">
            Pastikan perubahan yang Anda buat sudah disimpan sebelum keluar dari sesi admin.
        </p>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <div class="modal-actions">
                <button type="button" class="btn" id="cancelLogoutBtn">Batal</button>
                <button type="submit" class="btn pri btn-danger-solid">Ya, Keluar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Hamburger menu (mobile)
    const menuBtn = document.getElementById('menuToggleBtn');
    const sideNav = document.getElementById('sideNav');
    if (menuBtn && sideNav) {
        menuBtn.addEventListener('click', function () {
            const open = sideNav.classList.toggle('open');
            menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            menuBtn.textContent = open ? '✕' : '☰';
        });
    }

    // Modal View Site
    const viewSiteModal = document.getElementById('viewSiteModal');
    const triggerViewSite = document.getElementById('triggerViewSiteBtn');
    const cancelViewSite = document.getElementById('cancelViewSiteBtn');
    const confirmViewSite = document.getElementById('confirmViewSiteBtn');

    if (triggerViewSite) {
        triggerViewSite.addEventListener('click', () => viewSiteModal.classList.add('open'));
        cancelViewSite.addEventListener('click', () => viewSiteModal.classList.remove('open'));
        confirmViewSite.addEventListener('click', () => viewSiteModal.classList.remove('open'));
        viewSiteModal.addEventListener('click', e => { if (e.target === viewSiteModal) viewSiteModal.classList.remove('open'); });
    }

    // Modal Logout
    const logoutModal = document.getElementById('logoutModal');
    const triggerLogout = document.getElementById('triggerLogoutBtn');
    const cancelLogout = document.getElementById('cancelLogoutBtn');

    if (triggerLogout) {
        triggerLogout.addEventListener('click', () => logoutModal.classList.add('open'));
        cancelLogout.addEventListener('click', () => logoutModal.classList.remove('open'));
        logoutModal.addEventListener('click', e => { if (e.target === logoutModal) logoutModal.classList.remove('open'); });
    }

    // Escape Key for Global Modals
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (viewSiteModal) viewSiteModal.classList.remove('open');
            if (logoutModal) logoutModal.classList.remove('open');
            if (sideNav && sideNav.classList.contains('open')) {
                sideNav.classList.remove('open');
                if (menuBtn) {
                    menuBtn.setAttribute('aria-expanded', 'false');
                    menuBtn.textContent = '☰';
                }
            }
        }
    });
});
</script>
</body>
</html>
