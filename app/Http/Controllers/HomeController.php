<?php

namespace App\Http\Controllers;

use App\Models\GalleryPhoto;
use App\Models\InfoChip;
use App\Models\Material;
use App\Models\OrderStep;
use App\Models\SiteSetting;
use App\Models\Term;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $photos = GalleryPhoto::query()->with('materials')->orderBy('sort_order')->get();

        $rawPhotos = $photos->map(function ($p) {
            $materialIds = $p->materials->pluck('id')->map(fn ($id) => (string) $id)->toArray();
            if ($p->material_id && ! in_array((string) $p->material_id, $materialIds)) {
                $materialIds[] = (string) $p->material_id;
            }

            return [
                'id' => $p->id,
                'src' => $p->url(),
                'caption' => $p->caption,
                'material_ids' => $materialIds,
                'material_names' => $p->materials->pluck('name')->map(fn ($n) => strtolower($n))->toArray(),
            ];
        });

        return view('home', [
            'setting' => SiteSetting::current(),
            'chips' => InfoChip::query()->orderBy('sort_order')->get(),
            'photos' => $photos,
            'rawPhotos' => $rawPhotos,
            'materials' => Material::query()->orderBy('sort_order')->get(),
            'steps' => OrderStep::query()->orderBy('sort_order')->get(),
            'terms' => Term::query()->orderBy('sort_order')->get(),
        ]);
    }
}
