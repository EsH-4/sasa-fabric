@extends('admin.layouts.app')
@section('title', 'Ringkasan')
@section('content')
<div class="stats">
    <div class="stat"><b>{{ $catalogCount }}</b><span>Katalog Kain</span></div>
    <div class="stat"><b>{{ $materialCount }}</b><span>Material Kain</span></div>
    <div class="stat"><b>{{ $stepCount }}</b><span>Langkah Pemesanan</span></div>
    <div class="stat"><b>{{ $termCount }}</b><span>Syarat &amp; Ketentuan</span></div>
</div>
<div class="card card-mt">
    <h2>Kelola Konten</h2>
    <p class="muted">Kelola produk katalog kain, teks informasi, dan WhatsApp dari panel ini.</p>
    <div class="actions">
        <a class="btn pri" href="{{ route('admin.catalog.index') }}">Kelola Katalog Kain</a>
        <a class="btn" href="{{ route('admin.settings.edit') }}">Pengaturan Halaman</a>
    </div>
</div>
@endsection
