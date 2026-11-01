<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSetting;
use App\Models\InfoChip;
use App\Models\Material;
use App\Models\OrderStep;
use App\Models\SiteSetting;
use App\Models\Term;
use App\Support\LineList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $setting = SiteSetting::current();

        return view('admin.settings.edit', [
            'setting' => $setting,
            'homeSetting' => HomeSetting::current(),
            'info' => InfoChip::query()->orderBy('sort_order')->pluck('text')->implode("\n"),
            'koleksi' => Material::query()->orderBy('sort_order')->pluck('name')->implode("\n"),
            'cara' => OrderStep::query()->orderBy('sort_order')->pluck('text')->implode("\n"),
            'sk' => Term::query()->orderBy('sort_order')->pluck('text')->implode("\n"),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'judul' => ['nullable', 'string', 'max:255'],
            'headline' => ['nullable', 'string', 'max:255'],
            'subjudul' => ['nullable', 'string'],
            'wa' => ['nullable', 'string', 'max:30'],
            'wa_tampil' => ['nullable', 'string', 'max:30'],
            'ig' => ['nullable', 'string', 'max:80'],
            'katalog' => ['nullable', 'string', 'max:255'],
            'pesan' => ['nullable', 'string'],
            'tombol1' => ['nullable', 'string', 'max:255'],
            'tombol2' => ['nullable', 'string', 'max:255'],
            'judul_bestseller' => ['nullable', 'string', 'max:255'],
            'judul_katalog' => ['nullable', 'string', 'max:255'],
            'judul_galeri' => ['nullable', 'string', 'max:255'],
            'judul_koleksi' => ['nullable', 'string', 'max:255'],
            'judul_cara' => ['nullable', 'string', 'max:255'],
            'judul_sk' => ['nullable', 'string', 'max:255'],
            'footer' => ['nullable', 'string'],
            'info' => ['nullable', 'string'],
            'koleksi' => ['nullable', 'string'],
            'cara' => ['nullable', 'string'],
            'sk' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:4096'],
        ]);

        $setting = SiteSetting::current();

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logo', 'public');
        }

        if ($request->has('judul_bestseller')) {
            HomeSetting::current()->update([
                'judul_bestseller' => $request->input('judul_bestseller') ?: 'Bestseller',
            ]);
        }

        unset($data['info'], $data['koleksi'], $data['cara'], $data['sk'], $data['logo'], $data['judul_bestseller']);

        if ($request->filled('wa')) {
            $rawWa = (string) $request->input('wa');
            $digits = preg_replace('/\D/', '', $rawWa);
            if ($digits !== '') {
                if (str_starts_with($digits, '0')) {
                    $data['wa'] = '62'.substr($digits, 1);
                    $data['wa_tampil'] = $digits;
                } elseif (str_starts_with($digits, '62')) {
                    $data['wa'] = $digits;
                    $data['wa_tampil'] = '0'.substr($digits, 2);
                } else {
                    $data['wa'] = $digits;
                    $data['wa_tampil'] = $rawWa;
                }
            }
        } elseif ($request->has('wa')) {
            $data['wa'] = null;
            $data['wa_tampil'] = null;
        }

        $setting->update($data);

        if ($request->has('info')) {
            $this->sync(InfoChip::class, 'text', LineList::parse($request->input('info')));
        }
        $this->sync(Material::class, 'name', LineList::parse($request->input('koleksi')));
        $this->sync(OrderStep::class, 'text', LineList::parse($request->input('cara')));
        $this->sync(Term::class, 'text', LineList::parse($request->input('sk')));

        return back()->with('status', 'Halaman berhasil disimpan.');
    }

    /**
     * @param  class-string  $model
     * @param  list<string>  $lines
     */
    private function sync(string $model, string $column, array $lines): void
    {
        $model::query()->delete();

        foreach ($lines as $i => $line) {
            $model::query()->create([
                $column => $line,
                'sort_order' => $i,
            ]);
        }
    }
}
