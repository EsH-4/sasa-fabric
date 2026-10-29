<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryPhoto;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        return view('admin.gallery.index', [
            'photos' => GalleryPhoto::query()->with('materials')->orderBy('sort_order')->get(),
            'materials' => Material::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'photos' => ['required', 'array'],
            'photos.*' => ['image', 'max:8192'],
            'materials' => ['nullable', 'array'],
            'materials.*' => ['exists:materials,id'],
        ]);

        $sort = (int) GalleryPhoto::query()->max('sort_order');
        $materialIds = $request->input('materials', []);

        foreach ($request->file('photos', []) as $file) {
            $sort++;
            $photo = GalleryPhoto::query()->create([
                'material_id' => $materialIds[0] ?? null,
                'path' => $file->store('gallery', 'public'),
                'caption' => '',
                'sort_order' => $sort,
            ]);

            if (! empty($materialIds)) {
                $photo->materials()->sync($materialIds);
            }
        }

        return back()->with('status', 'Foto berhasil ditambahkan.');
    }

    public function update(Request $request, GalleryPhoto $photo): RedirectResponse
    {
        $data = $request->validate([
            'caption' => ['nullable', 'string', 'max:255'],
            'materials' => ['nullable', 'array'],
            'materials.*' => ['exists:materials,id'],
        ]);

        $materialIds = $request->input('materials', []);
        $data['material_id'] = $materialIds[0] ?? null;

        $photo->update($data);
        $photo->materials()->sync($materialIds);

        return back()->with('status', 'Keterangan dan bahan kain berhasil disimpan.');
    }

    public function destroy(GalleryPhoto $photo): RedirectResponse
    {
        Storage::disk('public')->delete($photo->path);
        $photo->materials()->detach();
        $photo->delete();

        return back()->with('status', 'Foto dihapus.');
    }

    public function move(GalleryPhoto $photo, string $direction): RedirectResponse
    {
        $swap = GalleryPhoto::query()
            ->when(
                $direction === 'up',
                fn ($q) => $q->where('sort_order', '<', $photo->sort_order)->orderByDesc('sort_order'),
                fn ($q) => $q->where('sort_order', '>', $photo->sort_order)->orderBy('sort_order')
            )
            ->first();

        if ($swap) {
            [$photo->sort_order, $swap->sort_order] = [$swap->sort_order, $photo->sort_order];
            $photo->save();
            $swap->save();
        }

        return back();
    }
}
