<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Barang;
use App\Models\Kelompok;
use App\Models\Kemasan;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\Mutasibarang;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::with(['kelompokRel', 'kemasanRel', 'satuanRel'])->paginate(20);
        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        $kelompoks = Kelompok::all();
        $kemasans = Kemasan::all();
        $satuans = Satuan::all();
        $suppliers = Supplier::all();
        return view('barang.create', compact('kelompoks', 'kemasans', 'satuans', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kodebarang' => 'required|unique:barang,kodebarang',
            'namabarang' => 'required',
            'kelompok' => 'required',
            'kemasan' => 'required',
            'satuan' => 'required',
            'hargabeli' => 'required|numeric',
        ]);

        \DB::transaction(function() use ($request) {
            Barang::create($request->all());
            
            // Create initial mutasibarang
            Mutasibarang::create([
                'kodebarang' => $request->kodebarang,
                'saldoawal' => 0,
                'beli' => 0,
                'returbeli' => 0,
                'keluar' => 0,
                'returkeluar' => 0,
                'jual' => 0,
                'returjual' => 0,
                'rakit' => 0,
                'adjustmen' => 0,
                'saldoakhir' => 0
            ]);
        });

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $kelompoks = Kelompok::all();
        $kemasans = Kemasan::all();
        $satuans = Satuan::all();
        $suppliers = Supplier::all();
        return view('barang.edit', compact('barang', 'kelompoks', 'kemasans', 'satuans', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);
        $request->validate([
            'namabarang' => 'required',
            'hargabeli' => 'required|numeric',
        ]);

        $barang->update($request->all());

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        // Should check if stock exists before deleting
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
    }
}
