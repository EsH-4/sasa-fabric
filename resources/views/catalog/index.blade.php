<!DOCTYPE html>
<html lang="id" data-theme="light" style="color-scheme: light;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Katalog Kain · {{ $setting->judul ?: 'Sasa Fabric' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
</head>
<body>

@include('partials.navbar')

<!-- Hero Katalog -->
<header class="hero hero-catalog">
    <div class="in">
        @if ($setting->logoUrl())
            <div class="logo">
                <img src="{{ $setting->logoUrl() }}" alt="Sasa Fabric">
            </div>
        @endif
        <p class="eyebrow">{{ $setting->judul }}</p>
        <h1>{{ $setting->judul_katalog ?: 'Katalog Koleksi Kain' }}</h1>
        <p class="lead">
            Pilihan kain berkualitas tinggi dengan tekstur mewah dan kenyamanan optimal. Klik pada kain untuk melihat rincian dan memesan langsung via WhatsApp.
        </p>

        <!-- Search Bar -->
        <div class="catalog-search-wrap">
            <input type="text" id="catalog-search" placeholder="Cari nama kain, komposisi, atau kode..." aria-label="Cari kain">
            <span class="search-clear-btn" id="search-clear" hidden>&times;</span>
        </div>
    </div>
</header>
<div class="strip" aria-hidden="true"></div>

<main class="wrap wrap-wide">
    <!-- Filter Material -->
    @if ($materials->isNotEmpty())
        <section class="no-line section-filters">
            <div class="mat" id="catalog-filters">
                <button type="button" class="btn-all active" data-filter="all">Semua Kain</button>
                @foreach ($materials as $material)
                    <button type="button" class="item-mat" data-filter="{{ $material->id }}" data-name="{{ strtolower($material->name) }}">
                        {{ $material->name }}
                    </button>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Count status -->
    <div class="catalog-status-bar">
        <span id="catalog-count-text">Menampilkan semua kain</span>
    </div>

    <!-- Catalog Grid -->
    <div class="catalog-main-grid" id="catalog-container">
        @forelse ($items as $item)
            @php
                $mIds = $item->materials->pluck('id')->toArray();
                $mNames = $item->materials->pluck('name')->toArray();
            @endphp
            <div class="catalog-card-item"
                id="item-{{ $item->id }}"
                data-id="{{ $item->id }}"
                data-name="{{ strtolower($item->name) }}"
                data-code="{{ strtolower($item->code ?? '') }}"
                data-materials="{{ strtolower(implode(' ', $mNames)) }}"
                data-material-ids="{{ implode(',', $mIds) }}"
                data-price="{{ $item->price ?? '' }}"
                data-desc="{{ $item->description ?? '' }}"
                data-available="{{ $item->is_available ? '1' : '0' }}"
                data-src="{{ $item->mediaUrl() }}"
                data-media-type="{{ $item->isVideo() ? 'video' : 'image' }}"
                data-wa-url="{{ $item->waOrderUrl($setting) }}">
                
                @if ($item->isVideo())
                    <div class="catalog-card-media catalog-card-media-video" role="button" tabindex="0" aria-label="Lihat detail {{ $item->name }}">
                        <video src="{{ $item->mediaUrl() }}" autoplay muted loop playsinline preload="auto" class="catalog-card-video"></video>
                        @if ($item->price)
                            <span class="catalog-pill-price">{{ $item->price }}</span>
                        @endif
                        @if (! $item->is_available)
                            <span class="catalog-pill-status">Tutup PO</span>
                        @endif
                        <div class="catalog-video-badge">🎬 Video</div>
                        <div class="catalog-zoom-hint">Klik untuk detail</div>
                    </div>
                @else
                    <div class="catalog-card-media" style="background-image:url('{{ $item->mediaUrl() }}')" role="button" tabindex="0" aria-label="Lihat detail {{ $item->name }}">
                        @if ($item->price)
                            <span class="catalog-pill-price">{{ $item->price }}</span>
                        @endif
                        @if (! $item->is_available)
                            <span class="catalog-pill-status">Tutup PO</span>
                        @endif
                        <div class="catalog-zoom-hint">Klik untuk perbesar</div>
                    </div>
                @endif

                <div class="catalog-card-info">
                    <h3 class="catalog-item-title">{{ $item->name }}</h3>
                    @if ($item->code)
                        <p class="catalog-item-spec">{{ $item->code }}</p>
                    @endif
                    
                    @if (! empty($mNames))
                        <div class="catalog-card-chips">
                            @foreach ($mNames as $matName)
                                <span class="chip">{{ $matName }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if ($item->description)
                        <p class="catalog-item-desc">{{ Str::limit($item->description, 80) }}</p>
                    @endif

                </div>
            </div>
        @empty
            <div class="empty-catalog-box">
                <p>Belum ada produk di katalog kain.</p>
            </div>
        @endforelse
    </div>

    <p id="empty-catalog-filter" class="empty-filter">Tidak ditemukan kain yang sesuai dengan kata kunci atau filter material.</p>

    <!-- Footer & Contact -->
    <div class="contact">
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

<!-- Detail / Zoom Modal Popup -->
<div id="catalogModal" class="modal-backdrop">
    <div class="modal-box modal-catalog-detail" role="dialog" aria-modal="true">
        <button type="button" class="modal-close" id="closeDetailBtn" aria-label="Tutup">&times;</button>
        <div class="modal-catalog-grid">
            <div class="modal-catalog-img-wrap">
                <img id="modalDetailImg" src="" alt="Foto Kain" title="Klik untuk perbesar layar penuh">
                <button type="button" class="modal-img-fullscreen-btn" id="openPhotoFsBtn" aria-label="Perbesar Layar Penuh" title="Perbesar Layar Penuh">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>
                    </svg>
                    <span>Layar Penuh</span>
                </button>
                <video id="modalDetailVid" src="" controls playsinline loop hidden style="width:100%;height:100%;object-fit:cover;display:block;border-radius:8px;background:#000;"></video>
            </div>
            <div class="modal-catalog-content">
                <h2 id="modalDetailName" class="modal-cat-title"></h2>
                <div id="modalDetailCode" class="modal-cat-code"></div>
                <div id="modalDetailPrice" class="modal-cat-price"></div>
                <div id="modalDetailMaterials" class="modal-cat-materials"></div>
                <div id="modalDetailDesc" class="modal-cat-desc"></div>

                <div class="modal-cat-cta">
                    <a id="modalDetailWaBtn" href="" target="_blank" rel="noopener noreferrer" class="btn-wa-order btn-wa-order-lg">
                        <svg viewBox="0 0 24 24" class="btn-wa-icon" aria-hidden="true">
                            <path fill="currentColor" d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>Pesan Kain Ini via WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Photo Lightbox -->
<div id="photoFsModal" class="photo-fs-backdrop" aria-hidden="true" role="dialog" aria-modal="true">
    <button type="button" class="photo-fs-close" id="closePhotoFsBtn" aria-label="Tutup Layar Penuh">&times;</button>
    <div class="photo-fs-container">
        <img id="photoFsImg" src="" alt="Foto Kain Layar Penuh">
    </div>
    <div class="photo-fs-hint">Klik gambar atau tekan ESC untuk keluar</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('catalog-search');
    const searchClear = document.getElementById('search-clear');
    const filterButtons = document.querySelectorAll('#catalog-filters button');
    const cards = Array.from(document.querySelectorAll('.catalog-card-item'));
    const emptyMsg = document.getElementById('empty-catalog-filter');
    const countText = document.getElementById('catalog-count-text');

    let currentFilter = 'all';
    let currentFilterName = '';
    let currentQuery = '';

    function filterCatalog() {
        let visibleCount = 0;
        const q = currentQuery.trim().toLowerCase();

        cards.forEach(card => {
            const name = card.dataset.name || '';
            const code = card.dataset.code || '';
            const mats = card.dataset.materials || '';
            const mIds = (card.dataset.materialIds || '').split(',').filter(Boolean);

            const matchFilter = (currentFilter === 'all') || 
                                mIds.includes(currentFilter) || 
                                (currentFilterName && (name.includes(currentFilterName) || mats.includes(currentFilterName)));

            const matchQuery = !q || name.includes(q) || code.includes(q) || mats.includes(q);

            if (matchFilter && matchQuery) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyMsg) {
            emptyMsg.classList.toggle('is-visible', visibleCount === 0);
        }

        if (countText) {
            if (visibleCount === cards.length) {
                countText.textContent = 'Menampilkan semua kain (' + visibleCount + ' koleksi)';
            } else {
                countText.textContent = 'Menampilkan ' + visibleCount + ' dari ' + cards.length + ' koleksi';
            }
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentQuery = this.value;
            if (searchClear) searchClear.hidden = (currentQuery === '');
            filterCatalog();
        });
    }

    if (searchClear) {
        searchClear.addEventListener('click', function () {
            searchInput.value = '';
            currentQuery = '';
            this.hidden = true;
            searchInput.focus();
            filterCatalog();
        });
    }

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            currentFilterName = this.dataset.name || '';
            filterCatalog();
        });
    });

    // Slider Edge Fades
    const filterWrap = document.querySelector('.section-filters');
    const filterScroll = document.getElementById('catalog-filters');
    if (filterWrap && filterScroll) {
        const updateFade = () => {
            filterWrap.classList.toggle('has-scroll-left', filterScroll.scrollLeft > 6);
            filterWrap.classList.toggle('at-scroll-end', filterScroll.scrollLeft >= (filterScroll.scrollWidth - filterScroll.clientWidth - 8));
        };
        filterScroll.addEventListener('scroll', updateFade, { passive: true });
        window.addEventListener('resize', updateFade);
        updateFade();
    }

    // Auto-play video cards on hover (public catalog)
    cards.forEach(card => {
        const video = card.querySelector('.catalog-card-video');
        if (video) {
            const mediaDiv = card.querySelector('.catalog-card-media-video');
            if (mediaDiv) {
                mediaDiv.addEventListener('mouseenter', () => { video.play().catch(() => {}); });
                mediaDiv.addEventListener('mouseleave', () => { video.pause(); video.currentTime = 0; });
            }
        }
    });

    // Detail Modal
    const modal = document.getElementById('catalogModal');
    const closeBtn = document.getElementById('closeDetailBtn');
    const modalImg = document.getElementById('modalDetailImg');
    const modalVid = document.getElementById('modalDetailVid');
    const modalName = document.getElementById('modalDetailName');
    const modalCode = document.getElementById('modalDetailCode');
    const modalPrice = document.getElementById('modalDetailPrice');
    const modalDesc = document.getElementById('modalDetailDesc');
    const modalMats = document.getElementById('modalDetailMaterials');
    const modalWa = document.getElementById('modalDetailWaBtn');
    const openPhotoFsBtn = document.getElementById('openPhotoFsBtn');
    const photoFsModal = document.getElementById('photoFsModal');
    const photoFsImg = document.getElementById('photoFsImg');
    const closePhotoFsBtn = document.getElementById('closePhotoFsBtn');

    function openPhotoFs() {
        if (!modalImg || !modalImg.src || modalImg.hidden) return;
        photoFsImg.src = modalImg.src;
        photoFsModal.classList.add('open');
        photoFsModal.setAttribute('aria-hidden', 'false');
    }

    function closePhotoFs() {
        if (!photoFsModal) return;
        photoFsModal.classList.remove('open');
        photoFsModal.setAttribute('aria-hidden', 'true');
        setTimeout(() => {
            if (!photoFsModal.classList.contains('open')) {
                photoFsImg.removeAttribute('src');
            }
        }, 200);
    }

    if (openPhotoFsBtn) {
        openPhotoFsBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            openPhotoFs();
        });
    }

    if (modalImg) {
        modalImg.addEventListener('click', openPhotoFs);
    }

    if (closePhotoFsBtn) {
        closePhotoFsBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            closePhotoFs();
        });
    }

    if (photoFsModal) {
        photoFsModal.addEventListener('click', function () {
            closePhotoFs();
        });
    }

    const videoExts = ['mp4', 'webm', 'mov', 'avi'];
    function isVideoUrl(url) {
        if (!url) return false;
        const clean = url.split('?')[0].split('#')[0].toLowerCase();
        const ext = clean.split('.').pop();
        return videoExts.includes(ext);
    }

    function openDetail(card) {
        const title = card.querySelector('.catalog-item-title')?.textContent || '';
        const code = card.querySelector('.catalog-item-spec')?.textContent || '';
        const price = card.dataset.price || '';
        const desc = card.dataset.desc || '';
        const available = card.dataset.available !== '0';
        const src = card.dataset.src || '';
        const mediaType = card.dataset.mediaType || 'image';
        const isVideo = (mediaType === 'video') || isVideoUrl(src);
        const waUrl = card.dataset.waUrl || '';
        const chipsHtml = card.querySelector('.catalog-card-chips')?.innerHTML || '';

        // Show appropriate media in modal
        if (isVideo) {
            modalImg.hidden = true;
            modalImg.style.display = 'none';
            modalImg.removeAttribute('src');
            if (openPhotoFsBtn) openPhotoFsBtn.hidden = true;

            modalVid.hidden = false;
            modalVid.style.display = 'block';
            modalVid.src = src;
            modalVid.load();
            const playPromise = modalVid.play();
            if (playPromise !== undefined) {
                playPromise.catch(() => {});
            }
        } else {
            modalVid.pause();
            modalVid.hidden = true;
            modalVid.style.display = 'none';
            modalVid.removeAttribute('src');

            modalImg.hidden = false;
            modalImg.style.display = 'block';
            modalImg.src = src;
            if (openPhotoFsBtn) openPhotoFsBtn.hidden = false;
        }

        modalName.textContent = title;
        modalCode.textContent = code ? 'Kode: ' + code : '';
        modalPrice.innerHTML = (price ? 'Harga: ' + price : 'Sistem PO') + (available ? '' : ' <span class="catalog-pill-status" style="position:static;display:inline-block;margin-left:6px;vertical-align:middle;">Tutup PO</span>');
        modalDesc.textContent = desc;
        modalMats.innerHTML = chipsHtml;
        modalWa.href = waUrl;

        modal.classList.add('open');
    }

    function closeDetail() {
        closePhotoFs();
        modal.classList.remove('open');
        // Pause and reset video when closing modal
        if (modalVid) {
            modalVid.pause();
            modalVid.removeAttribute('src');
            modalVid.style.display = 'none';
            modalVid.load();
        }
    }

    cards.forEach(card => {
        const media = card.querySelector('.catalog-card-media, .catalog-card-media-video');
        if (media) {
            media.addEventListener('click', () => openDetail(card));
            media.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') openDetail(card); });
        }
    });

    if (closeBtn) closeBtn.addEventListener('click', closeDetail);
    if (modal) modal.addEventListener('click', e => { if (e.target === modal) closeDetail(); });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            if (photoFsModal && photoFsModal.classList.contains('open')) {
                closePhotoFs();
            } else if (modal.classList.contains('open')) {
                closeDetail();
            }
        }
    });
});
</script>
</body>
</html>
