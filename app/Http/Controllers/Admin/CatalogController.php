<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogItem;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        return view('admin.catalog.index', [
            'items' => CatalogItem::query()->with('materials')->orderBy('sort_order')->get(),
            'materials' => Material::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'media' => ['required', 'file', 'max:51200', 'mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov,avi'],
            'is_available' => ['nullable'],
            'is_featured' => ['nullable'],
            'materials' => ['nullable', 'array'],
            'materials.*' => ['exists:materials,id'],
        ]);

        $file = $request->file('media');
        $sort = ((int) CatalogItem::query()->max('sort_order')) + 1;
        $path = $file->store('catalog', 'public');
        $mediaType = CatalogItem::detectMediaType($file->getClientOriginalName());

        $item = CatalogItem::query()->create([
            'name' => $data['name'],
            'code' => $data['code'] ?? null,
            'price' => $data['price'] ?? null,
            'description' => $data['description'] ?? null,
            'image_path' => $path,
            'media_type' => $mediaType,
            'is_available' => $request->boolean('is_available'),
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => $sort,
        ]);

        if (! empty($data['materials'])) {
            $item->materials()->sync($data['materials']);
        }

        return back()->with('status', 'Kain berhasil ditambahkan ke katalog.');
    }

    public function update(Request $request, CatalogItem $catalog): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'media' => ['nullable', 'file', 'max:51200', 'mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov,avi'],
            'is_available' => ['nullable'],
            'is_featured' => ['nullable'],
            'materials' => ['nullable', 'array'],
            'materials.*' => ['exists:materials,id'],
        ]);

        if ($request->hasFile('media')) {
            if ($catalog->image_path) {
                Storage::disk('public')->delete($catalog->image_path);
            }
            $file = $request->file('media');
            $catalog->image_path = $file->store('catalog', 'public');
            $catalog->media_type = CatalogItem::detectMediaType($file->getClientOriginalName());
        }

        $catalog->name = $data['name'];
        $catalog->code = $data['code'] ?? null;
        $catalog->price = $data['price'] ?? null;
        $catalog->description = $data['description'] ?? null;
        $catalog->is_available = $request->boolean('is_available');
        $catalog->is_featured = $request->boolean('is_featured');
        $catalog->save();

        $catalog->materials()->sync($request->input('materials', []));

        return back()->with('status', 'Katalog kain berhasil diperbarui.');
    }

    public function destroy(CatalogItem $catalog): RedirectResponse
    {
        if ($catalog->image_path) {
            Storage::disk('public')->delete($catalog->image_path);
        }
        $catalog->materials()->detach();
        $catalog->delete();

        return back()->with('status', 'Kain dihapus dari katalog.');
    }

    public function move(CatalogItem $catalog, string $direction): RedirectResponse
    {
        $swap = CatalogItem::query()
            ->when(
                $direction === 'up',
                fn ($q) => $q->where('sort_order', '<', $catalog->sort_order)->orderByDesc('sort_order'),
                fn ($q) => $q->where('sort_order', '>', $catalog->sort_order)->orderBy('sort_order')
            )
            ->first();

        if ($swap) {
            [$catalog->sort_order, $swap->sort_order] = [$swap->sort_order, $catalog->sort_order];
            $catalog->save();
            $swap->save();
        }

        return back();
    }
}
