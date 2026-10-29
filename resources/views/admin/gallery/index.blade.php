@extends('admin.layouts.app')
@section('title', 'Galeri foto')
@section('content')
<div class="card page-head">
    <div>
        <h2>Koleksi Foto Galeri</h2>
        <span class="muted">Kelola foto kain pilihan yang tampil di halaman depan.</span>
    </div>
    <button type="button" class="btn pri btn-lg" id="openAddPhotoBtn">
        + Tambah Foto Kain
    </button>
</div>

<div class="gallery">
    @forelse ($photos as $photo)
        @php
            $mNames = $photo->materials->pluck('name')->toArray();
            if (empty($mNames) && $photo->material) {
                $mNames[] = $photo->material->name;
            }
            $mIds = $photo->materials->pluck('id')->toArray();
            if ($photo->material_id && !in_array($photo->material_id, $mIds)) {
                $mIds[] = $photo->material_id;
            }
        @endphp
        <div class="gitem">
            <img src="{{ $photo->url() }}" alt="{{ $photo->caption }}">
            <div class="gitem-body">
                <div class="gitem-caption">
                    {{ $photo->caption ?: '(Belum ada keterangan / kode kain)' }}
                </div>
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
                <button type="button" class="btn btn-edit-photo"
                    data-id="{{ $photo->id }}"
                    data-caption="{{ $photo->caption }}"
                    data-material-ids="{{ json_encode($mIds) }}"
                    data-src="{{ $photo->url() }}"
                    data-action="{{ route('admin.gallery.update', $photo) }}">
                    Edit
                </button>
                <form method="POST" action="{{ route('admin.gallery.move', [$photo, 'up']) }}">@csrf<button class="btn" type="submit" title="Geser ke kiri/atas">←</button></form>
                <form method="POST" action="{{ route('admin.gallery.move', [$photo, 'down']) }}">@csrf<button class="btn" type="submit" title="Geser ke kanan/bawah">→</button></form>
                <button type="button" class="btn del btn-delete-photo"
                    data-caption="{{ $photo->caption ?: 'Tanpa keterangan' }}"
                    data-src="{{ $photo->url() }}"
                    data-action="{{ route('admin.gallery.destroy', $photo) }}"
                    title="Hapus foto">
                    Hapus
                </button>
            </div>
        </div>
    @empty
        <p class="muted">Belum ada foto. Klik tombol <b>+ Tambah Foto Kain</b> di atas untuk mengunggah.</p>
    @endforelse
</div>

<!-- Modal Popup Tambah Foto -->
<div id="addPhotoModal" class="modal-backdrop">
    <div class="modal-box modal-box-md" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h3>Unggah Foto Kain Baru</h3>
            <button type="button" class="modal-close" id="closeAddModalBtn">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="custom-file-upload">
                <input type="file" name="photos[]" id="addPhotoInput" accept="image/*" multiple required>
                <div class="upload-text-main">Klik atau Tarik Foto-Foto Kain ke Sini</div>
                <div class="upload-text-sub">Bisa pilih sekaligus banyak foto (JPG, PNG, WebP)</div>
                <div id="addPhotoStatus" class="custom-file-status" hidden></div>
            </div>
            <div id="addPreviewGrid" class="preview-grid"></div>

            <label class="f f-gap-sm">
                <span>Pilih Bahan / Material (Bisa centang lebih dari satu untuk kain campuran)</span>
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
                <button type="button" class="btn" id="cancelAddModalBtn">Batal</button>
                <button type="submit" class="btn pri">Unggah Sekarang</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Popup Edit Foto -->
<div id="editPhotoModal" class="modal-backdrop">
    <div class="modal-box modal-box-md" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h3>Edit Kain &amp; Bahan</h3>
            <button type="button" class="modal-close" id="closeModalBtn">&times;</button>
        </div>
        <img id="modalPreviewImg" class="modal-preview" src="" alt="Preview Foto">
        <form id="modalEditForm" method="POST" action="">
            @csrf
            @method('PUT')
            <label class="f">
                <span>Keterangan (Kode kain / Komposisi)</span>
                <input type="text" name="caption" id="modalCaptionInput" placeholder="Contoh: Cupro Twill (66% Cupro 30% Viscose 4% Spandex)">
            </label>

            <label class="f f-gap-sm">
                <span>Bahan / Material Kain (Bisa pilih lebih dari satu)</span>
            </label>
            <div class="material-checkbox-grid" id="editMaterialGrid">
                @foreach ($materials as $m)
                    <label class="material-checkbox-label">
                        <input type="checkbox" name="materials[]" value="{{ $m->id }}" class="edit-material-checkbox" id="mat_chk_{{ $m->id }}">
                        <span>{{ $m->name }}</span>
                    </label>
                @endforeach
            </div>

            <div class="actions modal-actions-end modal-actions-tall">
                <button type="button" class="btn" id="cancelModalBtn">Batal</button>
                <button type="submit" class="btn pri">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Popup Verifikasi Hapus Foto -->
<div id="deletePhotoModal" class="modal-backdrop">
    <div class="modal-box modal-box-sm" role="dialog" aria-modal="true">
        <h3 class="modal-title">Hapus Foto Ini?</h3>
        <p class="muted modal-text">
            Foto yang dihapus tidak dapat dikembalikan lagi dari galeri.
        </p>
        <div class="delete-summary">
            <img id="deletePreviewImg" src="" alt="Foto">
            <div class="delete-summary-body">
                <span class="delete-summary-label">Keterangan:</span>
                <b id="deleteCaptionText" class="delete-summary-caption"></b>
            </div>
        </div>
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="modal-actions">
                <button type="button" class="btn modal-btn-confirm" id="cancelDeleteBtn">Batal</button>
                <button type="submit" class="btn pri btn-danger-solid">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Edit Modal Logic ---
    const editModal = document.getElementById('editPhotoModal');
    const editForm = document.getElementById('modalEditForm');
    const previewImg = document.getElementById('modalPreviewImg');
    const captionInput = document.getElementById('modalCaptionInput');
    const editCheckboxes = document.querySelectorAll('.edit-material-checkbox');
    const closeEditBtn = document.getElementById('closeModalBtn');
    const cancelEditBtn = document.getElementById('cancelModalBtn');

    function openEditModal(data) {
        editForm.action = data.action;
        previewImg.src = data.src;
        captionInput.value = data.caption || '';
        
        // Reset and check matching checkboxes
        const selectedIds = Array.isArray(data.materialIds) ? data.materialIds.map(Number) : [];
        editCheckboxes.forEach(chk => {
            chk.checked = selectedIds.includes(Number(chk.value));
        });

        editModal.classList.add('open');
        captionInput.focus();
    }

    function closeEditModal() {
        editModal.classList.remove('open');
    }

    document.querySelectorAll('.btn-edit-photo').forEach(btn => {
        btn.addEventListener('click', function () {
            let materialIds = [];
            try {
                materialIds = JSON.parse(this.dataset.materialIds || '[]');
            } catch (e) {
                materialIds = [];
            }
            openEditModal({
                id: this.dataset.id,
                caption: this.dataset.caption,
                materialIds: materialIds,
                src: this.dataset.src,
                action: this.dataset.action
            });
        });
    });

    closeEditBtn.addEventListener('click', closeEditModal);
    cancelEditBtn.addEventListener('click', closeEditModal);
    editModal.addEventListener('click', e => { if (e.target === editModal) closeEditModal(); });

    // --- Add Modal Logic ---
    const addModal = document.getElementById('addPhotoModal');
    const openAddBtn = document.getElementById('openAddPhotoBtn');
    const closeAddBtn = document.getElementById('closeAddModalBtn');
    const cancelAddBtn = document.getElementById('cancelAddModalBtn');
    const addPhotoInput = document.getElementById('addPhotoInput');
    const addPreviewGrid = document.getElementById('addPreviewGrid');

    function openAddModal() {
        addModal.classList.add('open');
    }
    function closeAddModal() {
        addModal.classList.remove('open');
    }

    if (openAddBtn) openAddBtn.addEventListener('click', openAddModal);
    closeAddBtn.addEventListener('click', closeAddModal);
    cancelAddBtn.addEventListener('click', closeAddModal);
    addModal.addEventListener('click', e => { if (e.target === addModal) closeAddModal(); });

    if (addPhotoInput) {
        addPhotoInput.addEventListener('change', function () {
            addPreviewGrid.innerHTML = '';
            const statusEl = document.getElementById('addPhotoStatus');
            if (this.files && this.files.length > 0) {
                if (statusEl) {
                    statusEl.textContent = '✓ ' + this.files.length + ' foto dipilih';
                    statusEl.hidden = false;
                }
                Array.from(this.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        addPreviewGrid.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
    }

    // --- Delete Modal Logic ---
    const deleteModal = document.getElementById('deletePhotoModal');
    const deleteForm = document.getElementById('deleteForm');
    const deletePreviewImg = document.getElementById('deletePreviewImg');
    const deleteCaptionText = document.getElementById('deleteCaptionText');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

    function openDeleteModal(data) {
        deleteForm.action = data.action;
        deletePreviewImg.src = data.src;
        deleteCaptionText.textContent = data.caption;
        deleteModal.classList.add('open');
    }

    function closeDeleteModal() {
        deleteModal.classList.remove('open');
    }

    document.querySelectorAll('.btn-delete-photo').forEach(btn => {
        btn.addEventListener('click', function () {
            openDeleteModal({
                caption: this.dataset.caption,
                src: this.dataset.src,
                action: this.dataset.action
            });
        });
    });

    cancelDeleteBtn.addEventListener('click', closeDeleteModal);
    deleteModal.addEventListener('click', e => { if (e.target === deleteModal) closeDeleteModal(); });

    // Keyboard ESC for all modals
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeEditModal();
            closeAddModal();
            closeDeleteModal();
        }
    });
});
</script>
@endsection
