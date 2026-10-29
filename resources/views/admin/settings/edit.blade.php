@extends('admin.layouts.app')
@section('title', 'Edit halaman')
@section('content')
@if ($errors->any())
    <div class="alert">{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="card">
    @csrf
    @method('PUT')
    <h2>Hero & kontak</h2>
    
    <!-- Bagian Logo dengan Popup -->
    <div class="section-box">
        <span class="section-box-label">Logo Website</span>
        <div class="logo-row">
            <div id="logoPreviewWrapper" class="logo-preview-box">
                @if ($setting->logoUrl())
                    <img id="currentLogoImg" src="{{ $setting->logoUrl() }}" alt="Logo">
                @else
                    <span id="noLogoText" class="muted logo-none-text">(Belum ada logo)</span>
                @endif
            </div>
            <div>
                <button type="button" class="btn" id="openLogoModalBtn">
                    Pilih / Ganti Logo
                </button>
                <span id="selectedLogoLabel" class="logo-hint">
                    Gunakan file PNG transparan untuk hasil terbaik.
                </span>
            </div>
        </div>
        <!-- Hidden input actual file yang akan dikirim saat form disimpan -->
        <input type="file" name="logo" id="actualLogoInput" accept="image/*" class="visually-hidden">
    </div>

    <div class="grid-2">
        <label class="f"><span>Nama kecil di atas judul</span><input type="text" name="judul" value="{{ old('judul', $setting->judul) }}"></label>
        <label class="f"><span>Judul besar</span><input type="text" name="headline" value="{{ old('headline', $setting->headline) }}"></label>
    </div>
    <label class="f"><span>Subjudul</span><textarea name="subjudul" rows="3">{{ old('subjudul', $setting->subjudul) }}</textarea></label>
    <div class="grid-2">
        <label class="f"><span>Nomor WhatsApp</span><input type="text" name="wa" value="{{ old('wa', $setting->wa_tampil ?: $setting->wa) }}" placeholder="Contoh: 085174108440"></label>
        <label class="f"><span>Instagram (tanpa @)</span><input type="text" name="ig" value="{{ old('ig', $setting->ig) }}" placeholder="sasa.fabric"></label>
    </div>
    <label class="f"><span>Pesan Default WhatsApp</span><input type="text" name="pesan" value="{{ old('pesan', $setting->pesan) }}" placeholder="Halo Sasa, saya mau tanya soal katalog kain Sasa Fabric."></label>

    <h2>Judul section</h2>
    <div class="grid-2">
        <label class="f"><span>Judul galeri foto</span><input type="text" name="judul_galeri" value="{{ old('judul_galeri', $setting->judul_galeri) }}"></label>
        <label class="f"><span>Judul material</span><input type="text" name="judul_koleksi" value="{{ old('judul_koleksi', $setting->judul_koleksi) }}"></label>
        <label class="f"><span>Judul cara pesan</span><input type="text" name="judul_cara" value="{{ old('judul_cara', $setting->judul_cara) }}"></label>
        <label class="f"><span>Judul syarat &amp; ketentuan</span><input type="text" name="judul_sk" value="{{ old('judul_sk', $setting->judul_sk) }}"></label>
    </div>
    <label class="f"><span>Daftar material (satu baris satu)</span><textarea name="koleksi" rows="6">{{ old('koleksi', $koleksi) }}</textarea></label>
    <label class="f"><span>Langkah cara pesan (satu baris satu)</span><textarea name="cara" rows="5">{{ old('cara', $cara) }}</textarea></label>
    <label class="f"><span>Syarat &amp; ketentuan (satu baris satu poin)</span><textarea name="sk" rows="10">{{ old('sk', $sk) }}</textarea></label>
    <label class="f"><span>Footer</span><textarea name="footer" rows="2">{{ old('footer', $setting->footer) }}</textarea></label>
    <button class="btn pri" type="submit">Simpan &amp; terbitkan</button>
</form>

<!-- Modal Popup Ganti Logo -->
<div id="logoModal" class="modal-backdrop">
    <div class="modal-box" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h3>Pilih Logo Website</h3>
            <button type="button" class="modal-close" id="closeLogoModalBtn">&times;</button>
        </div>
        <div class="logo-modal-center">
            <div class="logo-modal-frame">
                <img id="modalLogoPreview" src="{{ $setting->logoUrl() ?: '' }}" alt="Preview Logo" class="{{ $setting->logoUrl() ? '' : 'is-hidden' }}">
                <div id="modalLogoPlaceholder" class="logo-placeholder {{ $setting->logoUrl() ? 'is-hidden' : '' }}">
                    Belum ada file dipilih
                </div>
            </div>
        </div>
        <div class="custom-file-upload">
            <input type="file" id="modalTempLogoInput" accept="image/*">
            <div class="upload-text-main">Klik atau Tarik File Gambar ke Sini</div>
            <div class="upload-text-sub">Format PNG transparan, JPG, SVG, atau WebP</div>
            <div id="modalLogoStatus" class="custom-file-status" hidden></div>
        </div>
        <div class="actions modal-actions-end modal-actions-lg">
            <button type="button" class="btn" id="cancelLogoModalBtn">Batal</button>
            <button type="button" class="btn pri" id="confirmLogoBtn">Gunakan Logo Ini</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('logoModal');
    const openBtn = document.getElementById('openLogoModalBtn');
    const closeBtn = document.getElementById('closeLogoModalBtn');
    const cancelBtn = document.getElementById('cancelLogoModalBtn');
    const confirmBtn = document.getElementById('confirmLogoBtn');
    
    const tempInput = document.getElementById('modalTempLogoInput');
    const actualInput = document.getElementById('actualLogoInput');
    const modalPreview = document.getElementById('modalLogoPreview');
    const modalPlaceholder = document.getElementById('modalLogoPlaceholder');
    const selectedLabel = document.getElementById('selectedLogoLabel');
    const previewWrapper = document.getElementById('logoPreviewWrapper');

    let currentSelectedFile = null;

    function openModal() {
        modal.classList.add('open');
    }
    function closeModal() {
        modal.classList.remove('open');
    }

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    tempInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            currentSelectedFile = this.files[0];
            const statusEl = document.getElementById('modalLogoStatus');
            if (statusEl) {
                statusEl.textContent = '✓ ' + currentSelectedFile.name;
                statusEl.hidden = false;
            }
            const reader = new FileReader();
            reader.onload = e => {
                modalPreview.src = e.target.result;
                modalPreview.classList.remove('is-hidden');
                modalPlaceholder.classList.add('is-hidden');
            };
            reader.readAsDataURL(currentSelectedFile);
        }
    });

    confirmBtn.addEventListener('click', function () {
        if (tempInput.files && tempInput.files[0]) {
            // Transfer file list ke actual hidden input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(tempInput.files[0]);
            actualInput.files = dataTransfer.files;

            // Update preview di halaman utama
            previewWrapper.innerHTML = '<img src="' + modalPreview.src + '" alt="Logo">';
            selectedLabel.innerHTML = '<b class="text-success">✓ Logo baru dipilih:</b> ' + tempInput.files[0].name + ' (Klik "Simpan & terbitkan" di bawah untuk menyimpan perubahan)';
        }
        closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('open')) {
            closeModal();
        }
    });
});
</script>
@endsection
