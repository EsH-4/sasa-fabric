<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\Material;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $setting = SiteSetting::current();
        $materials = Material::query()->orderBy('sort_order')->get();

        $items = CatalogItem::query()
            ->with('materials')
            ->orderBy('sort_order')
            ->get();

        $rawItems = $items->map(function ($item) use ($setting) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'code' => $item->code,
                'price' => $item->price,
                'description' => $item->description,
                'src' => $item->mediaUrl(),
                'media_type' => $item->isVideo() ? 'video' : 'image',
                'is_available' => $item->is_available,
                'is_featured' => $item->is_featured,
                'material_ids' => $item->materials->pluck('id')->map(fn ($id) => (string) $id)->toArray(),
                'material_names' => $item->materials->pluck('name')->map(fn ($n) => strtolower($n))->toArray(),
                'wa_url' => $item->waOrderUrl($setting),
            ];
        });

        return view('catalog.index', [
            'setting' => $setting,
            'materials' => $materials,
            'items' => $items,
            'rawItems' => $rawItems,
        ]);
    }
}
