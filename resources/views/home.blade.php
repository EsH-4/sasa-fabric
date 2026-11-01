<!DOCTYPE html>
<html lang="id" data-theme="light" style="color-scheme: light;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Sasa Fabric · {{ $setting->judul ?: 'Kain Pilihan' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
</head>
<body>

@include('partials.navbar')

<header class="hero">
    <div class="in">
        @if ($setting->logoUrl())
            <div class="logo">
                <img src="{{ $setting->logoUrl() }}" alt="Sasa Fabric">
            </div>
        @endif
        <p class="eyebrow">{{ $setting->judul }}</p>
        <h1>{{ $setting->headline }}</h1>
        @if ($setting->subjudul)
            <p class="lead">{{ $setting->subjudul }}</p>
        @endif

        <div class="btns">
            <a href="{{ route('catalog.index') }}" class="b1">Lihat Katalog Kain</a>
            @if ($setting->wa)
                <a href="{{ $setting->waUrl($setting->pesan) }}" target="_blank" rel="noopener noreferrer" class="b2">Tanya Admin WhatsApp</a>
            @endif
        </div>
    </div>
</header>
<div class="strip" aria-hidden="true"></div>

<main class="wrap">
    <!-- Bestseller Slider Section -->
    @if (isset($catalogItems) && $catalogItems->isNotEmpty())
        <section class="section-home-catalog" id="katalog-preview">
            <div class="section-head-row">
                <div>
                    <h2>{{ $homeSetting->judul_bestseller ?: 'Bestseller' }}</h2>
                    <p class="muted section-sub">Koleksi kain pilihan dengan kualitas terbaik untuk busana Anda.</p>
                </div>
            </div>

            <div class="catalog-slider-wrapper">
                <div class="catalog-slider-track" id="catSliderTrack">
                    @foreach ($catalogItems as $cItem)
                        <div class="cat-card cat-card-slide">
                            @if ($cItem->isVideo())
                                <a href="{{ route('catalog.index') }}#item-{{ $cItem->id }}" class="cat-card-img cat-card-img-video" aria-label="Lihat kain {{ $cItem->name }}">
                                    <video src="{{ $cItem->mediaUrl() }}" autoplay muted loop playsinline preload="auto" class="cat-card-video"></video>
                                    @if ($cItem->price)
                                        <span class="cat-price-pill">{{ $cItem->price }}</span>
                                    @endif
                                    @if (! $cItem->is_available)
                                        <span class="catalog-pill-status">Tutup PO</span>
                                    @endif
                                    <span class="catalog-video-badge" style="position:absolute;bottom:8px;left:8px;">🎬 Video</span>
                                </a>
                            @else
                                <a href="{{ route('catalog.index') }}#item-{{ $cItem->id }}" class="cat-card-img" style="background-image:url('{{ $cItem->mediaUrl() }}')" aria-label="Lihat kain {{ $cItem->name }}">
                                    @if ($cItem->price)
                                        <span class="cat-price-pill">{{ $cItem->price }}</span>
                                    @endif
                                    @if (! $cItem->is_available)
                                        <span class="catalog-pill-status">Tutup PO</span>
                                    @endif
                                </a>
                            @endif
                            <div class="cat-card-body">
                                <a href="{{ route('catalog.index') }}#item-{{ $cItem->id }}" class="cat-card-title-link">
                                    <h3 class="cat-card-title">{{ $cItem->name }}</h3>
                                </a>
                                @if ($cItem->code)
                                    <p class="cat-card-code">{{ $cItem->code }}</p>
                                @endif
                                <div class="cat-card-tags">
                                    @foreach ($cItem->materials as $mat)
                                        <span class="chip">{{ $mat->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="catalog-home-footer">
                <a href="{{ route('catalog.index') }}" class="b1 b-center">Lihat Semua Koleksi Kain</a>
            </div>
        </section>
    @endif

    <!-- Cara Pesan -->
    @if ($steps->isNotEmpty())
        <section id="cara-pesan">
            <h2>{{ $setting->judul_cara }}</h2>
            <ol class="steps">
                @foreach ($steps as $i => $step)
                    <li><b>{{ $i + 1 }}</b><span>{{ $step->text }}</span></li>
                @endforeach
            </ol>
        </section>
    @endif

    <!-- Syarat & Ketentuan -->
    @if ($terms->isNotEmpty())
        <section class="sk" id="syarat">
            <h2>{{ $setting->judul_sk }}</h2>
            <ol>
                @foreach ($terms as $term)
                    <li>{{ $term->text }}</li>
                @endforeach
            </ol>
        </section>
    @endif

    <!-- Kontak & Footer -->
    <div class="contact" id="kontak">
        @if ($setting->ig)
            <a href="https://instagram.com/{{ $setting->ig }}" target="_blank" rel="noopener">Instagram: {{ '@' . ltrim($setting->ig, '@') }}</a>
        @endif
        @if ($setting->ig && $setting->wa)
            <i></i>
        @endif
        @if ($setting->wa)
            <a href="{{ $setting->waUrl() }}" target="_blank" rel="noopener">WhatsApp: {{ $setting->wa_tampil }}</a>
        @endif
    </div>
    <footer>{{ $setting->footer }}</footer>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const track = document.getElementById('catSliderTrack');
    if (!track) return;

    const wrapper = track.parentElement;
    const originalCards = Array.from(track.querySelectorAll('.cat-card-slide'));
    const N = originalCards.length;
    if (N === 0) return;

    // Gandakan 2 set tambahan (total 3 set) untuk seamless circular loop
    for (let s = 0; s < 2; s++) {
        originalCards.forEach(function (card) {
            track.appendChild(card.cloneNode(true));
        });
    }

    const allCards = Array.from(track.querySelectorAll('.cat-card-slide'));
    let currentIndex = N; // Mulai dari set 1 (kartu pertama ada di tengah)
    let isPaused = false;
    let timer = null;
    const transitionStyle = 'transform 0.95s cubic-bezier(0.25, 1, 0.5, 1)';

    function setActive(index) {
        allCards.forEach(function (card, i) {
            if (i === index) {
                card.classList.add('is-center');
            } else {
                card.classList.remove('is-center');
            }
        });
    }

    function moveTo(index, animate) {
        const card = allCards[index] || allCards[0];
        const cardWidth = card.offsetWidth;
        const gap = parseFloat(window.getComputedStyle(track).gap) || 12;
        const step = cardWidth + gap;
        const x = ((wrapper.clientWidth - cardWidth) / 2) - (index * step);
        track.style.transition = animate ? transitionStyle : 'none';
        track.style.transform = 'translate3d(' + x + 'px, 0, 0)';
    }

    // Set posisi awal tanpa animasi
    function init() {
        setActive(currentIndex);
        moveTo(currentIndex, false);
    }

    requestAnimationFrame(function () {
        init();
    });

    function nextSlide() {
        if (isPaused) {
            timer = setTimeout(nextSlide, 1000);
            return;
        }

        currentIndex++;
        setActive(currentIndex);
        moveTo(currentIndex, true);

        // Jika sudah mencapai set 2, setelah animasi slide selesai (950ms), reset ke set 1 tanpa animasi
        if (currentIndex >= 2 * N) {
            setTimeout(function () {
                currentIndex = N;
                setActive(currentIndex);
                moveTo(currentIndex, false);
            }, 980);
        }

        timer = setTimeout(nextSlide, 3300);
    }

    timer = setTimeout(nextSlide, 3300);

    track.addEventListener('mouseenter', function () { isPaused = true; });
    track.addEventListener('mouseleave', function () { isPaused = false; });

    track.addEventListener('touchstart', function () {
        isPaused = true;
    }, { passive: true });

    track.addEventListener('touchend', function () {
        setTimeout(function () { isPaused = false; }, 1600);
    }, { passive: true });

    window.addEventListener('resize', function () {
        moveTo(currentIndex, false);
    });
});
</script>

</body>
</html>
