<?php

namespace Database\Seeders;

use App\Models\GalleryPhoto;
use App\Models\Material;
use Illuminate\Database\Seeder;

class CleanDuplicateMaterialsSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar baku standar material yang sudah diurutkan sesuai abjad (A - Z)
        $cleanList = [
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

        // Mapping alias untuk menangani nama terbalik / varian nama lain
        $aliases = [
            'lyocell / tencel' => 'Tencel / Lyocell',
            'tencel / lyocell' => 'Tencel / Lyocell',
            'tencel' => 'Tencel / Lyocell',
            'lyocell' => 'Tencel / Lyocell',
            'rayon / viscose' => 'Viscose / Rayon',
            'viscose / rayon' => 'Viscose / Rayon',
            'rayon' => 'Viscose / Rayon',
            'viscose' => 'Viscose / Rayon',
            'poliester / polyester' => 'Polyester / Poliester',
            'polyester / poliester' => 'Polyester / Poliester',
            'polyester' => 'Polyester / Poliester',
            'poliester' => 'Polyester / Poliester',
            'nilon / nylon' => 'Nylon / Nilon',
            'nylon / nilon' => 'Nylon / Nilon',
            'nylon' => 'Nylon / Nilon',
            'nilon' => 'Nylon / Nilon',
            'silk / sutra' => 'Sutra (Silk)',
            'sutra (silk)' => 'Sutra (Silk)',
            'sutra' => 'Sutra (Silk)',
            'silk' => 'Sutra (Silk)',
            'katun / cotton' => 'Cotton (Katun)',
            'cotton (katun)' => 'Cotton (Katun)',
            'katun' => 'Cotton (Katun)',
            'cotton' => 'Cotton (Katun)',
            'wal / wool' => 'Wool / Wal',
            'wool / wal' => 'Wool / Wal',
            'wool' => 'Wool / Wal',
            'wal' => 'Wool / Wal',
        ];

        $allMaterials = Material::query()->get();

        // 1. Buat atau perbarui material resmi sesuai urutan abjad
        $officialModels = [];
        foreach ($cleanList as $index => $standardName) {
            $norm = strtolower(trim($standardName));
            $existing = $allMaterials->first(fn ($m) => strtolower(trim($m->name)) === $norm);
            if (! $existing) {
                $existing = Material::query()->create([
                    'name' => $standardName,
                    'sort_order' => $index,
                ]);
            } else {
                $existing->update([
                    'name' => $standardName,
                    'sort_order' => $index,
                ]);
            }
            $officialModels[$standardName] = $existing;
        }

        // 2. Alihkan relasi foto dari duplikat ke nama resmi lalu hapus duplikatnya
        foreach ($allMaterials as $m) {
            $norm = strtolower(trim($m->name));
            $targetOfficialName = $aliases[$norm] ?? null;

            if ($targetOfficialName && isset($officialModels[$targetOfficialName])) {
                $targetId = $officialModels[$targetOfficialName]->id;
                if ($m->id !== $targetId) {
                    GalleryPhoto::query()->where('material_id', $m->id)->update(['material_id' => $targetId]);
                    $m->delete();
                    $this->command->info("Menghapus duplikat: '{$m->name}' -> dialihkan ke '{$targetOfficialName}'");
                }
            } elseif (! in_array($m->name, $cleanList)) {
                $m->delete();
                $this->command->warn("Menghapus: '{$m->name}'");
            }
        }

        $this->command->info('Data material berhasil diurutkan A-Z dan dirapikan!');
    }
}
