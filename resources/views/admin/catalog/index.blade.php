@extends('admin.layouts.app')
@section('title', 'Katalog Kain')
@section('content')
<div class="card page-head">
    <div>
        <h2>Koleksi Katalog Kain</h2>
        <span class="muted">Kelola daftar kain yang tampil di halaman katalog publik dan preview beranda.</span>
    </div>
    <div class="actions">
        <a href="{{ route('catalog.index') }}" target="_blank" class="btn">Lihat Katalog Publik &nearr;</a>
        <button type="button" class="btn pri btn-lg" id="openAddCatalogBtn">
            + Tambah Kain ke Katalog
        </button>
    </div>
</div>

<div class="gallery">
    @forelse ($items as $item)
        @php
            $mNames = $item->materials->pluck('name')->toArray();
            $mIds = $item->materials->pluck('id')->toArray();
        @endphp
        <div class="gitem catalog-gitem">
            @if ($item->isVideo())
                <video src="{{ $item->mediaUrl() }}" autoplay muted loop playsinline preload="auto" class="catalog-admin-video"></video>
            @else
                <img src="{{ $item->mediaUrl() }}" alt="{{ $item->name }}">
            @endif
            <div class="gitem-body">
                <div class="catalog-title-row">
                    <span class="catalog-item-name">{{ $item->name }}</span>
                    @if ($item->isVideo())
                        <span class="badge-status badge-video" title="Video">🎬 Video</span>
                    @endif
                    @if ($item->price)
                        <span class="badge-price">{{ $item->price }}</span>
                    @endif
                    @if ($item->is_featured)
                        <span class="badge-status badge-feat" title="Tampil di preview beranda">★ Beranda</span>
                    @endif
                    @if (! $item->is_available)
                        <span class="badge-status badge-po">Habis / Tutup PO</span>
                    @endif
                </div>

                @if ($item->code)
                    <div class="catalog-item-code">Kode/Komposisi: <b>{{ $item->code }}</b></div>
                @endif

                @if ($item->description)
                    <div class="catalog-item-desc">{{ Str::limit($item->description, 100) }}</div>
                @endif

                <div class="gitem-materials">
                    <span class="gitem-materials-label">Bahan:</span>
                    @forelse ($mNames as $mName)
                        <span class="chip">{{ $mName }}</span>
                    @empty
                        <span class="muted chip-empty">Semua / Tanpa Kategori</span>
                    @endforelse
                </div>
            </div>
            <div class="acts">
                <button type="button" class="btn btn-edit-catalog"
                    data-id="{{ $item->id }}"
                    data-name="{{ $item->name }}"
                    data-code="{{ $item->code }}"
                    data-price="{{ $item->price }}"
                    data-description="{{ $item->description }}"
                    data-available="{{ $item->is_available ? '1' : '0' }}"
                    data-featured="{{ $item->is_featured ? '1' : '0' }}"
                    data-material-ids="{{ json_encode($mIds) }}"
                    data-src="{{ $item->mediaUrl() }}"
                    data-media-type="{{ $item->isVideo() ? 'video' : 'image' }}"
                    data-action="{{ route('admin.catalog.update', $item) }}">
                    Edit
                </button>
                <form method="POST" action="{{ route('admin.catalog.move', [$item, 'up']) }}">@csrf<button class="btn" type="submit" title="Geser ke atas">↑</button></form>
                <form method="POST" action="{{ route('admin.catalog.move', [$item, 'down']) }}">@csrf<button class="btn" type="submit" title="Geser ke bawah">↓</button></form>
                <button type="button" class="btn del btn-delete-catalog"
                    data-name="{{ $item->name }}"
                    data-src="{{ $item->mediaUrl() }}"
                    data-media-type="{{ $item->isVideo() ? 'video' : 'image' }}"
                    data-action="{{ route('admin.catalog.destroy', $item) }}"
                    title="Hapus kain">
                    Hapus
                </button>
            </div>
        </div>
    @empty
        <div class="card">
            <p class="muted">Belum ada item di katalog. Klik tombol <b>+ Tambah Kain ke Katalog</b> di atas untuk menambahkan koleksi pertama.</p>
        </div>
    @endforelse
</div>

<!-- Modal Popup Tambah Kain -->
<div id="addCatalogModal" class="modal-backdrop">
    <div class="modal-box modal-box-md" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h3>Tambah Kain ke Katalog</h3>
            <button type="button" class="modal-close" id="closeAddCatalogBtn">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.catalog.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="custom-file-upload">
                <input type="file" name="media" id="addCatalogMediaInput" accept="image/*,video/mp4,video/webm,video/quicktime,video/x-msvideo" required>
                <div class="upload-text-main">Klik atau Tarik Foto / Video Kain ke Sini</div>
                <div class="upload-text-sub">Format JPG, PNG, WebP, MP4, WebM, atau MOV (maks. 50 MB)</div>
                <div id="addCatalogMediaStatus" class="custom-file-status" hidden></div>
            </div>
            <div id="addCatalogPreview" class="catalog-single-preview" hidden>
                <img id="addCatalogPreviewImg" src="" alt="Preview">
                <video id="addCatalogPreviewVid" src="" muted loop playsinline controls hidden></video>
            </div>

            <div class="grid-2">
                <label class="f">
                    <span>Nama Kain <b class="text-danger">*</b></span>
                    <input type="text" name="name" required placeholder="Contoh: Cupro Twill Luxury">
                </label>
                <label class="f">
                    <span>Kode / Komposisi Kain</span>
                    <input type="text" name="code" placeholder="Contoh: CT-01 (66% Cupro 30% Viscose 4% Spandex)">
                </label>
            </div>

            <div class="grid-2">
                <label class="f">
                    <span>Harga / Keterangan Harga</span>
                    <input type="text" name="price" placeholder="Contoh: Rp 85.000 / meter atau Sistem PO">
                </label>
                <div class="f">
                    <span>Opsi Tampilan</span>
                    <div class="checkbox-options-wrap">
                        <label class="checkbox-f">
                            <input type="checkbox" name="is_featured" value="1" checked>
                            <span>Tampilkan di Preview Beranda</span>
                        </label>
                        <label class="checkbox-f">
                            <input type="checkbox" name="is_available" value="1" checked>
                            <span>Status: Buka PO</span>
                        </label>
                    </div>
                </div>
            </div>

            <label class="f">
                <span>Deskripsi / Spesifikasi Kain (Opsional)</span>
                <textarea name="description" rows="2" placeholder="Contoh: Lebar kain 150cm, karakter jatuh & adem, sedikit stretch, cocok untuk blazer / kemeja / dress."></textarea>
            </label>

            <label class="f f-gap-sm">
                <span>Pilih Bahan / Material Kain (Bisa pilih lebih dari satu)</span>
            </label>
            <div class="material-checkbox-grid">
                @foreach ($materials as $m)
                    <label class="material-checkbox-label">
                        <input type="checkbox" name="materials[]" value="{{ $m->id }}">
                        <span>{{ $m->name }}</span>
                    </label>
                @endforeach
            </div>

            <div class="actions modal-actions-end modal-actions-tall">
                <button type="button" class="btn" id="cancelAddCatalogBtn">Batal</button>
                <button type="submit" class="btn pri">Simpan ke Katalog</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Popup Edit Kain -->
<div id="editCatalogModal" class="modal-backdrop">
    <div class="modal-box modal-box-md" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h3>Edit Kain Katalog</h3>
            <button type="button" class="modal-close" id="closeEditCatalogBtn">&times;</button>
        </div>
        <form id="editCatalogForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="edit-img-preview-row">
                <img id="editModalPreviewImg" class="edit-catalog-img" src="" alt="Preview">
                <video id="editModalPreviewVid" class="edit-catalog-img" src="" muted loop playsinline controls hidden></video>
                <div>
                    <label class="btn" style="cursor: pointer; display: inline-block;">
                        Ganti Foto / Video Kain
                        <input type="file" name="media" id="editCatalogMediaInput" accept="image/*,video/mp4,video/webm,video/quicktime,video/x-msvideo" class="visually-hidden">
                    </label>
                    <span id="editMediaStatus" class="muted" style="display: block; margin-top: 6px; font-size: 12px;">Kosongkan jika tidak ingin mengubah media.</span>
                </div>
            </div>

            <div class="grid-2">
                <label class="f">
                    <span>Nama Kain <b class="text-danger">*</b></span>
                    <input type="text" name="name" id="editCatalogName" required placeholder="Contoh: Cupro Twill Luxury">
                </label>
                <label class="f">
                    <span>Kode / Komposisi Kain</span>
                    <input type="text" name="code" id="editCatalogCode" placeholder="Contoh: CT-01 (66% Cupro 30% Viscose 4% Spandex)">
                </label>
            </div>

            <div class="grid-2">
                <label class="f">
                    <span>Harga / Keterangan Harga</span>
                    <input type="text" name="price" id="editCatalogPrice" placeholder="Contoh: Rp 85.000 / meter atau Sistem PO">
                </label>
                <div class="f">
                    <span>Opsi Tampilan</span>
                    <div class="checkbox-options-wrap">
                        <label class="checkbox-f">
                            <input type="checkbox" name="is_featured" id="editCatalogFeatured" value="1">
                            <span>Tampilkan di Preview Beranda</span>
                        </label>
                        <label class="checkbox-f">
                            <input type="checkbox" name="is_available" id="editCatalogAvailable" value="1">
                            <span>Status: Buka PO</span>
                        </label>
                    </div>
                </div>
            </div>

            <label class="f">
                <span>Deskripsi / Spesifikasi Kain (Opsional)</span>
                <textarea name="description" id="editCatalogDescription" rows="2" placeholder="Contoh: Lebar kain 150cm, karakter jatuh & adem, sedikit stretch, cocok untuk blazer / kemeja / dress."></textarea>
            </label>

            <label class="f f-gap-sm">
                <span>Bahan / Material Kain (Bisa pilih lebih dari satu)</span>
            </label>
            <div class="material-checkbox-grid">
                @foreach ($materials as $m)
                    <label class="material-checkbox-label">
                        <input type="checkbox" name="materials[]" value="{{ $m->id }}" class="edit-cat-material-checkbox" id="edit_cat_mat_{{ $m->id }}">
                        <span>{{ $m->name }}</span>
                    </label>
                @endforeach
            </div>

            <div class="actions modal-actions-end modal-actions-tall">
                <button type="button" class="btn" id="cancelEditCatalogBtn">Batal</button>
                <button type="submit" class="btn pri">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Popup Hapus Kain -->
<div id="deleteCatalogModal" class="modal-backdrop">
    <div class="modal-box modal-box-sm" role="dialog" aria-modal="true">
        <h3 class="modal-title">Hapus Kain dari Katalog?</h3>
        <p class="muted modal-text">
            Kain ini akan dihapus dari katalog publik dan tidak dapat dikembalikan.
        </p>
        <div class="delete-summary">
            <img id="deleteCatalogImg" src="" alt="Foto Kain">
            <video id="deleteCatalogVid" src="" muted loop playsinline hidden style="width:80px;height:80px;object-fit:cover;border-radius:8px"></video>
            <div class="delete-summary-body">
                <span class="delete-summary-label">Nama Kain:</span>
                <b id="deleteCatalogNameText" class="delete-summary-caption"></b>
            </div>
        </div>
        <form id="deleteCatalogForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="modal-actions">
                <button type="button" class="btn modal-btn-confirm" id="cancelDeleteCatalogBtn">Batal</button>
                <button type="submit" class="btn pri btn-danger-solid">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Helpers ---
    const videoExts = ['mp4', 'webm', 'mov', 'avi'];
    function isVideoFile(file) {
        if (!file) return false;
        if (file.type && file.type.startsWith('video/')) return true;
        const ext = file.name.split('.').pop().toLowerCase();
        return videoExts.includes(ext);
    }
    function isVideoUrl(url) {
        if (!url) return false;
        const ext = url.split('.').pop().split('?')[0].toLowerCase();
        return videoExts.includes(ext);
    }

    // --- Add Modal ---
    const addModal = document.getElementById('addCatalogModal');
    const openAddBtn = document.getElementById('openAddCatalogBtn');
    const closeAddBtn = document.getElementById('closeAddCatalogBtn');
    const cancelAddBtn = document.getElementById('cancelAddCatalogBtn');
    const addMediaInput = document.getElementById('addCatalogMediaInput');
    const addMediaStatus = document.getElementById('addCatalogMediaStatus');
    const addPreviewWrap = document.getElementById('addCatalogPreview');
    const addPreviewImg = document.getElementById('addCatalogPreviewImg');
    const addPreviewVid = document.getElementById('addCatalogPreviewVid');

    function openAddModal() { addModal.classList.add('open'); }
    function closeAddModal() { addModal.classList.remove('open'); }

    if (openAddBtn) openAddBtn.addEventListener('click', openAddModal);
    if (closeAddBtn) closeAddBtn.addEventListener('click', closeAddModal);
    if (cancelAddBtn) cancelAddBtn.addEventListener('click', closeAddModal);
    addModal.addEventListener('click', e => { if (e.target === addModal) closeAddModal(); });

    if (addMediaInput) {
        addMediaInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const isVid = isVideoFile(file);
                addMediaStatus.textContent = '✓ ' + file.name + (isVid ? ' (Video)' : ' (Foto)');
                addMediaStatus.hidden = false;

                if (isVid) {
                    // Video preview
                    const url = URL.createObjectURL(file);
                    addPreviewVid.src = url;
                    addPreviewVid.hidden = false;
                    addPreviewImg.hidden = true;
                    addPreviewImg.src = '';
                } else {
                    // Image preview
                    addPreviewVid.hidden = true;
                    addPreviewVid.src = '';
                    addPreviewImg.hidden = false;
                    const reader = new FileReader();
                    reader.onload = e => {
                        addPreviewImg.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
                addPreviewWrap.hidden = false;
            }
        });
    }

    // --- Edit Modal ---
    const editModal = document.getElementById('editCatalogModal');
    const editForm = document.getElementById('editCatalogForm');
    const editName = document.getElementById('editCatalogName');
    const editCode = document.getElementById('editCatalogCode');
    const editPrice = document.getElementById('editCatalogPrice');
    const editDesc = document.getElementById('editCatalogDescription');
    const editFeatured = document.getElementById('editCatalogFeatured');
    const editAvailable = document.getElementById('editCatalogAvailable');
    const editPreviewImg = document.getElementById('editModalPreviewImg');
    const editPreviewVid = document.getElementById('editModalPreviewVid');
    const editMediaInput = document.getElementById('editCatalogMediaInput');
    const editMediaStatus = document.getElementById('editMediaStatus');
    const closeEditBtn = document.getElementById('closeEditCatalogBtn');
    const cancelEditBtn = document.getElementById('cancelEditCatalogBtn');
    const editCheckboxes = document.querySelectorAll('.edit-cat-material-checkbox');

    function showEditPreview(src, mediaType) {
        if (mediaType === 'video' || isVideoUrl(src)) {
            editPreviewVid.src = src;
            editPreviewVid.hidden = false;
            editPreviewImg.hidden = true;
            editPreviewImg.src = '';
        } else {
            editPreviewImg.src = src;
            editPreviewImg.hidden = false;
            editPreviewVid.hidden = true;
            editPreviewVid.src = '';
        }
    }

    function openEditModal(data) {
        editForm.action = data.action;
        editName.value = data.name || '';
        editCode.value = data.code || '';
        editPrice.value = data.price || '';
        editDesc.value = data.description || '';
        editFeatured.checked = data.featured === '1';
        editAvailable.checked = data.available === '1';
        editMediaStatus.textContent = 'Kosongkan jika tidak ingin mengubah media.';

        showEditPreview(data.src, data.mediaType);

        const selectedIds = Array.isArray(data.materialIds) ? data.materialIds.map(Number) : [];
        editCheckboxes.forEach(chk => {
            chk.checked = selectedIds.includes(Number(chk.value));
        });

        editModal.classList.add('open');
        editName.focus();
    }

    function closeEditModal() { editModal.classList.remove('open'); }

    document.querySelectorAll('.btn-edit-catalog').forEach(btn => {
        btn.addEventListener('click', function () {
            let materialIds = [];
            try {
                materialIds = JSON.parse(this.dataset.materialIds || '[]');
            } catch (e) {
                materialIds = [];
            }
            openEditModal({
                id: this.dataset.id,
                name: this.dataset.name,
                code: this.dataset.code,
                price: this.dataset.price,
                description: this.dataset.description,
                featured: this.dataset.featured,
                available: this.dataset.available,
                materialIds: materialIds,
                src: this.dataset.src,
                mediaType: this.dataset.mediaType || 'image',
                action: this.dataset.action
            });
        });
    });

    if (closeEditBtn) closeEditBtn.addEventListener('click', closeEditModal);
    if (cancelEditBtn) cancelEditBtn.addEventListener('click', closeEditModal);
    editModal.addEventListener('click', e => { if (e.target === editModal) closeEditModal(); });

    if (editMediaInput) {
        editMediaInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const isVid = isVideoFile(file);
                editMediaStatus.innerHTML = '<b class="text-success">✓ File baru dipilih:</b> ' + file.name + (isVid ? ' (Video)' : ' (Foto)');
                
                if (isVid) {
                    const url = URL.createObjectURL(file);
                    showEditPreview(url, 'video');
                } else {
                    const reader = new FileReader();
                    reader.onload = e => {
                        showEditPreview(e.target.result, 'image');
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
    }

    // --- Delete Modal ---
    const deleteModal = document.getElementById('deleteCatalogModal');
    const deleteForm = document.getElementById('deleteCatalogForm');
    const deleteImg = document.getElementById('deleteCatalogImg');
    const deleteVid = document.getElementById('deleteCatalogVid');
    const deleteNameText = document.getElementById('deleteCatalogNameText');
    const cancelDeleteBtn = document.getElementById('cancelDeleteCatalogBtn');

    function openDeleteModal(data) {
        deleteForm.action = data.action;
        deleteNameText.textContent = data.name;

        if (data.mediaType === 'video' || isVideoUrl(data.src)) {
            deleteVid.src = data.src;
            deleteVid.hidden = false;
            deleteImg.hidden = true;
            deleteImg.src = '';
        } else {
            deleteImg.src = data.src;
            deleteImg.hidden = false;
            deleteVid.hidden = true;
            deleteVid.src = '';
        }

        deleteModal.classList.add('open');
    }

    function closeDeleteModal() { deleteModal.classList.remove('open'); }

    document.querySelectorAll('.btn-delete-catalog').forEach(btn => {
        btn.addEventListener('click', function () {
            openDeleteModal({
                name: this.dataset.name,
                src: this.dataset.src,
                mediaType: this.dataset.mediaType || 'image',
                action: this.dataset.action
            });
        });
    });

    if (cancelDeleteBtn) cancelDeleteBtn.addEventListener('click', closeDeleteModal);
    deleteModal.addEventListener('click', e => { if (e.target === deleteModal) closeDeleteModal(); });

    // Global ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAddModal();
            closeEditModal();
            closeDeleteModal();
        }
    });
});
</script>
@endsection
