<?php

namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use App\Models\MasterKategoriSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KategoriSampahController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $items = KategoriSampah::query()
            ->with('masterKategori')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama_sampah', 'like', "%{$q}%")
                        ->orWhereHas('masterKategori', function ($k) use ($q) {
                            $k->where('nama_kategori', 'like', "%{$q}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('Admin.KategoriSampah.index', compact('items', 'q'));
    }

    public function create()
    {
        $kategoriMaster = MasterKategoriSampah::orderBy('nama_kategori')->get();
        return view('Admin.KategoriSampah.create', compact('kategoriMaster'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sampah'        => ['required', 'string', 'max:100', 'unique:kategori_sampah,nama_sampah'],
            'master_kategori_id' => ['required', 'exists:master_kategori_sampah,id'], // WAJIB
            'deskripsi'          => ['nullable', 'string'],
            'harga_satuan'       => ['nullable', 'numeric', 'min:0'],
            'jenis_satuan'       => ['nullable', 'string', 'max:50'],
            'gambar_sampah'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('gambar_sampah')) {
            $validated['gambar_sampah'] = $request->file('gambar_sampah')->store('kategori_sampah', 'public');
        }

        KategoriSampah::create($validated);

        return redirect()->route('kategori_sampah.index')->with('success', 'Data sampah berhasil ditambahkan.');
    }

    // CATATAN: show kamu sebelumnya untuk Setoran, biarkan jika memang dipakai.
    public function show($id)
    {
        $setoran = \App\Models\SetoranSampah::with(['items.kategori'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.setoran.show', compact('setoran'));
    }

    public function edit(string $id)
    {
        $item = KategoriSampah::findOrFail($id);
        $kategoriMaster = MasterKategoriSampah::orderBy('nama_kategori')->get();

        return view('Admin.KategoriSampah.edit', compact('item', 'kategoriMaster'));
    }

    public function update(Request $request, string $id)
    {
        $item = KategoriSampah::findOrFail($id);

        $validated = $request->validate([
            'nama_sampah'        => ['required', 'string', 'max:100', 'unique:kategori_sampah,nama_sampah,' . $item->id],
            'master_kategori_id' => ['required', 'exists:master_kategori_sampah,id'], // WAJIB
            'deskripsi'          => ['nullable', 'string'],
            'harga_satuan'       => ['nullable', 'numeric', 'min:0'],
            'jenis_satuan'       => ['nullable', 'string', 'max:50'],
            'gambar_sampah'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('gambar_sampah')) {
            if ($item->gambar_sampah && Storage::disk('public')->exists($item->gambar_sampah)) {
                Storage::disk('public')->delete($item->gambar_sampah);
            }

            $validated['gambar_sampah'] = $request->file('gambar_sampah')->store('kategori_sampah', 'public');
        }

        $item->update($validated);

        return redirect()->route('kategori_sampah.index')->with('success', 'Data sampah berhasil diupdate.');
    }

    public function destroy(string $id)
    {
        $item = KategoriSampah::findOrFail($id);

        if ($item->gambar_sampah && Storage::disk('public')->exists($item->gambar_sampah)) {
            Storage::disk('public')->delete($item->gambar_sampah);
        }

        $item->delete();

        return redirect()->route('kategori_sampah.index')->with('success', 'Data sampah berhasil dihapus.');
    }
}
