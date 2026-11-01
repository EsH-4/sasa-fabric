<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\HomeSetting;
use App\Models\InfoChip;
use App\Models\OrderStep;
use App\Models\SiteSetting;
use App\Models\Term;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $catalogItems = CatalogItem::query()
            ->with('materials')
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->take(12)
            ->get();

        return view('home', [
            'setting' => SiteSetting::current(),
            'homeSetting' => HomeSetting::current(),
            'chips' => InfoChip::query()->orderBy('sort_order')->get(),
            'catalogItems' => $catalogItems,
            'catalogCount' => CatalogItem::query()->count(),
            'steps' => OrderStep::query()->orderBy('sort_order')->get(),
            'terms' => Term::query()->orderBy('sort_order')->get(),
        ]);
    }
}
