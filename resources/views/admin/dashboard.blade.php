@extends('admin.layouts.app')
@section('title', 'Ringkasan')
@section('content')
<div class="stats">
    <div class="stat"><b>{{ $photoCount }}</b><span>Foto galeri</span></div>
    <div class="stat"><b>{{ $materialCount }}</b><span>Material</span></div>
    <div class="stat"><b>{{ $stepCount }}</b><span>Langkah pesan</span></div>
    <div class="stat"><b>{{ $termCount }}</b><span>Syarat & ketentuan</span></div>
</div>
<div class="card card-mt">
    <h2>Kelola konten</h2>
    <p class="muted">Semua teks, logo, WhatsApp, dan daftar material diubah di Edit halaman. Foto kain di Galeri foto.</p>
    <div class="actions">
        <a class="btn pri" href="{{ route('admin.settings.edit') }}">Edit halaman</a>
        <a class="btn" href="{{ route('admin.gallery.index') }}">Galeri foto</a>
    </div>
</div>
@endsection
