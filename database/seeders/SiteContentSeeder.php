<?php

namespace Database\Seeders;

use App\Models\GalleryPhoto;
use App\Models\InfoChip;
use App\Models\Material;
use App\Models\OrderStep;
use App\Models\SiteSetting;
use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $logoPath = $this->storeAsset(database_path('seeders/assets/logo.png'), 'logo.png');

        SiteSetting::query()->delete();
        SiteSetting::query()->create([
            'judul' => 'Fabric · Materials · Studio',
            'headline' => 'Kain pilihan untuk karya terbaikmu',
            'subjudul' => 'Lihat foto, harga, dan pesan kain PO langsung lewat katalog WhatsApp kami.',
            'wa' => '6285174108440',
            'wa_tampil' => '085174108440',
            'ig' => 'sasa.fabric',
            'katalog' => '',
            'pesan' => 'Halo Sasa, saya mau tanya soal katalog kain Sasa Fabric.',
            'tombol1' => 'Lihat Katalog di WhatsApp',
            'tombol2' => 'Tanya Admin',
            'judul_galeri' => 'Kain Pilihan',
            'judul_koleksi' => 'Material Tersedia',
            'judul_cara' => 'Cara Pesan',
            'judul_sk' => 'Syarat & Ketentuan',
            'footer' => 'Harga dan ketersediaan kain mengikuti katalog WhatsApp.',
            'logo_path' => $logoPath,
        ]);

        InfoChip::query()->delete();
        foreach (['Sistem PO', 'DP 50%'] as $i => $text) {
            InfoChip::query()->create(['text' => $text, 'sort_order' => $i]);
        }

        Material::query()->delete();
        $initialMaterials = [
            'Acetate',
            'Cotton (Katun)',
            'Cupro',
            'Linen',
            'Modal',
            'Nylon / Nilon',
            'Polyester / Poliester',
            'Spandex',
            'Sutra (Silk)',
            'Tencel / Lyocell',
            'Viscose / Rayon',
            'Wool / Wal',
        ];
        foreach ($initialMaterials as $i => $name) {
            Material::query()->create(['name' => $name, 'sort_order' => $i]);
        }

        OrderStep::query()->delete();
        foreach ([
            'Pilih kain di katalog WhatsApp',
            'Chat admin untuk memesan kain PO',
            'Bayar DP 50% untuk mengunci pesanan',
            'Pesanan diproses dan dikirim dalam 6-8 minggu setelah DP masuk',
        ] as $i => $text) {
            OrderStep::query()->create(['text' => $text, 'sort_order' => $i]);
        }

        Term::query()->delete();
        foreach ([
            'Semua kain sistem PO (pre-order).',
            'DP 50% saat pemesanan.',
            'Pengiriman 6-8 minggu. Jika barang terkena redline, pengiriman bisa mundur dengan lama yang bervariasi.',
            'Pesanan yang sudah DP tidak bisa dibatalkan.',
            'Perbedaan warna karena layar/proses produksi adalah hal wajar.',
            'Cek kain maksimal 7 hari setelah diterima.',
            'Komplain cacat mayor wajib foto/video unboxing.',
            'Cacat minor bukan kategori retur.',
            'Pembayaran hanya ke rekening BLU BY BCA DIGITAL 0002-3847-2111 a.n. Sasa Nursaidah.',
        ] as $i => $text) {
            Term::query()->create(['text' => $text, 'sort_order' => $i]);
        }

        GalleryPhoto::query()->delete();
        $captions = ['Cupro Jaguard', 'Linen', 'Lyocell', 'Tencel'];
        foreach (['01.jpg', '02.jpg', '03.jpg', '04.jpg'] as $i => $file) {
            $path = $this->storeAsset(database_path('seeders/assets/gallery/'.$file), 'gallery/'.$file);
            GalleryPhoto::query()->create([
                'path' => $path,
                'caption' => $captions[$i],
                'sort_order' => $i,
            ]);
        }
    }

    private function storeAsset(string $source, string $destination): string
    {
        if (! File::exists($source)) {
            return $destination;
        }

        Storage::disk('public')->put($destination, File::get($source));

        return $destination;
    }
}
