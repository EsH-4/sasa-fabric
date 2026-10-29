<!DOCTYPE html>
<html lang="id" data-theme="light" style="color-scheme: light;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Sasa Fabric</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
</head>
<body>
<header class="hero">
    <div class="in">
        @if ($setting->logoUrl())
            <div class="logo">
                <img src="{{ $setting->logoUrl() }}" alt="Sasa Fabric">
            </div>
        @endif
        <p class="eyebrow">{{ $setting->judul }}</p>
        <h1>{{ $setting->headline }}</h1>
    </div>
</header>
<div class="strip" aria-hidden="true"></div>
<main class="wrap">
    @if ($materials->isNotEmpty())
        <section class="no-line">
            <h2>{{ $setting->judul_koleksi }}</h2>
            <div class="mat" id="mat-filters">
                <button type="button" class="btn-all active" data-filter="all">Semua</button>
                @foreach ($materials as $material)
                    <button type="button" class="item-mat" data-filter="{{ $material->id }}" data-name="{{ strtolower($material->name) }}">{{ $material->name }}</button>
                @endforeach
            </div>
        </section>
    @endif

    @if ($photos->isNotEmpty())
        <section>
            <h2>{{ $setting->judul_galeri }}</h2>
            <div class="gal" id="gal-container">
                @foreach ($photos as $i => $photo)
                    @php
                        $mIds = $photo->materials->pluck('id')->toArray();
                        if ($photo->material_id && !in_array($photo->material_id, $mIds)) {
                            $mIds[] = $photo->material_id;
                        }
                    @endphp
                    <button class="ph" type="button" 
                        data-id="{{ $photo->id }}"
                        data-material-ids="{{ implode(',', $mIds) }}"
                        data-caption="{{ strtolower($photo->caption ?? '') }}"
                        aria-label="Lihat foto {{ $i + 1 }}" 
                        style="background-image:url('{{ $photo->url() }}')">
                        @if ($photo->caption)
                            <span>{{ $photo->caption }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
            <p id="empty-filter" class="empty-filter">Belum ada kain untuk kategori material ini.</p>
        </section>
    @endif

    @if ($steps->isNotEmpty())
        <section>
            <h2>{{ $setting->judul_cara }}</h2>
            <ol class="steps">
                @foreach ($steps as $i => $step)
                    <li><b>{{ $i + 1 }}</b><span>{{ $step->text }}</span></li>
                @endforeach
            </ol>
        </section>
    @endif

    @if ($terms->isNotEmpty())
        <section class="sk">
            <h2>{{ $setting->judul_sk }}</h2>
            <ol>
                @foreach ($terms as $term)
                    <li>{{ $term->text }}</li>
                @endforeach
            </ol>
        </section>
    @endif

    <div class="contact">
        @if ($setting->ig)
            <a href="https://instagram.com/{{ $setting->ig }}" target="_blank" rel="noopener">IG {{ $setting->ig }}</a>
        @endif
        @if ($setting->wa)
            @if ($setting->ig)<i></i>@endif
            <a href="{{ $setting->waUrl() }}" target="_blank" rel="noopener">WA {{ $setting->wa_tampil }}</a>
        @endif
    </div>
    <footer>{{ $setting->footer }}</footer>
</main>

@if ($setting->wa)
    <a href="{{ $setting->waUrl($setting->pesan) }}" 
       class="wa-float" 
       target="_blank" 
       rel="noopener noreferrer" 
       aria-label="Chat WhatsApp">
        <span class="wa-tooltip">Chat WhatsApp</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="wa-icon" aria-hidden="true">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
        </svg>
    </a>
@endif

<div id="lb" hidden></div>
<script type="application/json" id="raw-photos-data">{!! json_encode($rawPhotos, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
<script>
const RAW_PHOTOS = JSON.parse(document.getElementById('raw-photos-data').textContent);

let visiblePhotos = [...RAW_PHOTOS];
let LB = null, tx = null;
const lb = document.getElementById('lb');
const photoButtons = Array.from(document.querySelectorAll('.ph'));
const emptyMsg = document.getElementById('empty-filter');

function esc(x) {
    return String(x ?? '').replace(/[&<>"]/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]));
}

function drawLb() {
    if (LB === null) { lb.innerHTML = ''; lb.hidden = true; return; }
    const n = visiblePhotos.length, c = visiblePhotos[LB]?.caption;
    lb.hidden = false;
    lb.innerHTML = '<div class="lbx" role="dialog" aria-modal="true" aria-label="Foto kain"><button class="lx" data-l="x" aria-label="Tutup">×</button><img src="'+visiblePhotos[LB].src+'" alt="Foto kain '+(LB+1)+'">'+(n>1?'<button class="ln lp" data-l="p" aria-label="Sebelumnya">‹</button><button class="ln lq" data-l="n" aria-label="Berikutnya">›</button><div class="ld">'+visiblePhotos.map((_,k)=>'<i'+(k===LB?' class="on"':'')+'></i>').join('')+'</div>':'')+(c?'<div class="lc">'+esc(c)+'</div>':'')+'</div>';
}

// Filter handling
const filterButtons = document.querySelectorAll('#mat-filters button');
filterButtons.forEach(btn => {
    btn.addEventListener('click', function () {
        filterButtons.forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const filterVal = this.dataset.filter;
        const filterName = this.dataset.name;

        if (filterVal === 'all') {
            visiblePhotos = [...RAW_PHOTOS];
            photoButtons.forEach(el => el.style.display = '');
        } else {
            visiblePhotos = RAW_PHOTOS.filter(p => {
                const matchId = Array.isArray(p.material_ids) && p.material_ids.includes(filterVal);
                const matchText = filterName && p.caption && p.caption.toLowerCase().includes(filterName);
                return matchId || matchText;
            });

            photoButtons.forEach(el => {
                const pIds = (el.dataset.materialIds || '').split(',').filter(Boolean);
                const pCaption = el.dataset.caption;
                const matchId = pIds.includes(filterVal);
                const matchText = filterName && pCaption && pCaption.includes(filterName);
                el.style.display = (matchId || matchText) ? '' : 'none';
            });
        }

        if (emptyMsg) {
            emptyMsg.classList.toggle('is-visible', visiblePhotos.length === 0);
        }
    });
});

document.addEventListener('click', function (e) {
    const t = e.target.closest('button');
    if (!t) return;
    if (t.classList.contains('ph')) {
        const id = +t.dataset.id;
        const idx = visiblePhotos.findIndex(p => p.id === id);
        if (idx !== -1) {
            LB = idx;
            drawLb();
        }
        return;
    }
    if (t.dataset.l) {
        const L = t.dataset.l, n = visiblePhotos.length;
        if (L === 'x') LB = null;
        else LB = (LB + (L === 'n' ? 1 : n - 1)) % n;
        drawLb();
    }
});

document.addEventListener('keydown', function (e) {
    if (LB === null) return;
    const n = visiblePhotos.length;
    if (e.key === 'Escape') LB = null;
    else if (e.key === 'ArrowRight') LB = (LB + 1) % n;
    else if (e.key === 'ArrowLeft') LB = (LB + n - 1) % n;
    else return;
    drawLb();
});

lb.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, {passive:true});
lb.addEventListener('touchend', e => {
    if (tx == null || LB === null || visiblePhotos.length < 2) return;
    const dx = e.changedTouches[0].clientX - tx, n = visiblePhotos.length;
    tx = null;
    if (Math.abs(dx) > 50) { LB = (LB + (dx < 0 ? 1 : n - 1)) % n; drawLb(); }
});
</script>
</body>
</html>
