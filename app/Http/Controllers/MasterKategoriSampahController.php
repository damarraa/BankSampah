<?php

namespace App\Http\Controllers;

use App\Models\MasterKategoriSampah;
use Illuminate\Http\Request;

class MasterKategoriSampahController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $items = MasterKategoriSampah::query()
            ->when($q, function ($query) use ($q) {
                $query->where('nama_kategori', 'like', "%{$q}%");
            })
            ->orderBy('nama_kategori')
            ->paginate(10)
            ->withQueryString();

        return view('Admin.MasterKategoriSampah.index', compact('items', 'q'));
    }

    public function create()
    {
        return view('Admin.MasterKategoriSampah.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:master_kategori_sampah,nama_kategori'],
            'deskripsi'     => ['nullable', 'string'],
        ]);

        MasterKategoriSampah::create($validated);

        return redirect()
            ->route('master_kategori_sampah.index')
            ->with('success', 'Kategori master berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $item = MasterKategoriSampah::findOrFail($id);
        return view('Admin.MasterKategoriSampah.edit', compact('item'));
    }

    public function update(Request $request, string $id)
    {
        $item = MasterKategoriSampah::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:master_kategori_sampah,nama_kategori,' . $item->id],
            'deskripsi'     => ['nullable', 'string'],
        ]);

        $item->update($validated);

        return redirect()
            ->route('master_kategori_sampah.index')
            ->with('success', 'Kategori master berhasil diupdate.');
    }

    public function destroy(string $id)
    {
        $item = MasterKategoriSampah::findOrFail($id);
        $item->delete();

        return redirect()
            ->route('master_kategori_sampah.index')
            ->with('success', 'Kategori master berhasil dihapus.');
    }
}
