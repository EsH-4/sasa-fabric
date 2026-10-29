<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryPhoto;
use App\Models\Material;
use App\Models\OrderStep;
use App\Models\Term;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'photoCount' => GalleryPhoto::query()->count(),
            'materialCount' => Material::query()->count(),
            'stepCount' => OrderStep::query()->count(),
            'termCount' => Term::query()->count(),
        ]);
    }
}
