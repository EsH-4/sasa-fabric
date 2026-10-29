<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class AdditionalMaterialsSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            'Viscose / Rayon',
            'Lyocell / Tencel',
            'Cupro',
            'Acetate',
            'Polyester / Poliester',
            'Linen',
            'Sutra (Silk)',
            'Nylon / Nilon',
            'Cotton (Katun)',
            'Spandex',
            'Modal',
            'Wool / Wal',
        ];

        // Ambil data bahan yang sudah ada (lowercase untuk perbandingan)
        $existing = Material::query()->get();
        $maxSort = (int) Material::query()->max('sort_order');

        foreach ($materials as $item) {
            $normalizedNew = strtolower(preg_replace('/[^a-zA-Z]/', '', $item));

            $alreadyExists = $existing->contains(function ($m) use ($normalizedNew) {
                $normalizedExisting = strtolower(preg_replace('/[^a-zA-Z]/', '', $m->name));

                return $normalizedExisting === $normalizedNew
                    || str_contains($normalizedExisting, $normalizedNew)
                    || str_contains($normalizedNew, $normalizedExisting);
            });

            if (! $alreadyExists) {
                $maxSort++;
                $created = Material::query()->create([
                    'name' => $item,
                    'sort_order' => $maxSort,
                ]);
                $existing->push($created);
                $this->command->info("Ditambahkan: {$item}");
            } else {
                $this->command->line("Sudah ada (dilewati): {$item}");
            }
        }
    }
}
