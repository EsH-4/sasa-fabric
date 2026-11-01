<nav class="site-nav">
    <div class="site-nav-in">
        <div class="site-nav-links">
            <a href="{{ route('home') }}" class="site-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
            <a href="{{ route('catalog.index') }}" class="site-nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}">Katalog Kain</a>
            <a href="{{ request()->routeIs('home') ? '#cara-pesan' : route('home') . '#cara-pesan' }}" class="site-nav-link">Cara Pesan</a>
            <a href="{{ request()->routeIs('home') ? '#syarat' : route('home') . '#syarat' }}" class="site-nav-link">Syarat &amp; Ketentuan</a>
        </div>
    </div>
</nav>
