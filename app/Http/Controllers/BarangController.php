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
    public function index(Request $request)
    {
        $query = Barang::with(['kelompokRel', 'kemasanRel', 'satuanRel']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('namabarang', 'like', "%{$search}%")
                  ->orWhere('kodebarang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelompok')) {
            $query->where('kelompok', $request->kelompok);
        }

        if ($request->filled('supplier')) {
            $query->where('supplier', $request->supplier);
        }

        $barangs = $query->paginate(20);
        $kelompoks = Kelompok::all();
        $suppliers = Supplier::all();
        
        return view('barang.index', compact('barangs', 'kelompoks', 'suppliers'));
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
            'namabarang' => 'required',
            'kelompok' => 'required',
            'kemasan' => 'required',
            'satuan' => 'required',
            'hargabeli' => 'required|numeric',
        ]);

        \DB::transaction(function() use ($request) {
            $barang = Barang::create($request->all());
            
            // Create initial mutasibarang
            Mutasibarang::create([
                'kodebarang' => $barang->kodebarang,
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
