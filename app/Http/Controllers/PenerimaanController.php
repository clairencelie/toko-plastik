<?php

namespace App\Http\Controllers;

use App\Models\Penerimaan;
use App\Models\Penerimaandetail;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Ap;
use App\Services\InventoryService;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenerimaanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penerimaan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nopenerimaan', 'like', "%{$search}%")
                  ->orWhere('namasupplier', 'like', "%{$search}%");
            });
        }

        $penerimaans = $query->orderBy('tglpenerimaan', 'desc')->orderBy('nopenerimaan', 'desc')->paginate(20);
        return view('penerimaan.index', compact('penerimaans'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $barangs = Barang::with('stok')->get();
        $nopenerimaan = $this->generateNoPenerimaan();
        return view('penerimaan.create', compact('suppliers', 'barangs', 'nopenerimaan'));
    }

    public function show($id)
    {
        $penerimaan = Penerimaan::with(['details', 'supplierRel'])->findOrFail($id);
        return view('penerimaan.show', compact('penerimaan'));
    }

    public function edit($id)
    {
        if (auth()->user()->username !== 'hdy') {
            abort(403);
        }
        $penerimaan = Penerimaan::with(['details.barang.satuanRel'])->findOrFail($id);
        $suppliers = Supplier::all();
        $barangs = Barang::with('stok')->get();
        return view('penerimaan.edit', compact('penerimaan', 'suppliers', 'barangs'));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->username !== 'hdy') {
            abort(403);
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $penerimaan = Penerimaan::findOrFail($id);

                $grandtotal = $request->grandtotal;
                $tunai = min($request->tunai, $grandtotal);
                $kredit = max(0, $grandtotal - $tunai);

                $penerimaan->tglpenerimaan = $request->tglpenerimaan;
                $penerimaan->supplier = $request->supplier_id;
                $penerimaan->namasupplier = Supplier::find($request->supplier_id)?->keterangan ?? '-';
                $penerimaan->totalbarang = $grandtotal;
                $penerimaan->grandtotal = $grandtotal;
                $penerimaan->tunai = $tunai;
                $penerimaan->kredit = $kredit;
                $penerimaan->tgljatuhtempo = $request->tgljatuhtempo ?: $request->tglpenerimaan;
                $penerimaan->save();

                // Delete old details; DB triggers will reverse fifostock/mutasibarang automatically
                Penerimaandetail::where('nopenerimaan', $penerimaan->nopenerimaan)->delete();

                // Insert new details; DB triggers will update fifostock/mutasibarang automatically
                foreach ($request->items as $index => $item) {
                    $barang = Barang::with('satuanRel')->find($item['kodebarang']);
                    Penerimaandetail::create([
                        'nopenerimaan' => $penerimaan->nopenerimaan,
                        'kodebarang' => $item['kodebarang'],
                        'nourut' => $index + 1,
                        'satuan' => $barang->satuan,
                        'jumlah' => $item['jumlah'],
                        'harga' => $item['harga'],
                        'diskon' => 0,
                        'hargadiskon' => $item['harga'],
                        'subtotal' => $item['jumlah'] * $item['harga'],
                        'tglpenerimaan' => $penerimaan->tglpenerimaan,
                        'namasatuan' => $barang->satuanRel->keterangan ?? 'PCS',
                        'namabarang' => $barang->namabarang,
                    ]);
                }

                // Update AP if exists and belum ada pembayaran
                $ap = Ap::where('nopenerimaan', $penerimaan->nopenerimaan)->first();
                if ($ap && $ap->bayar == 0) {
                    $ap->total = $grandtotal;
                    $ap->tunai = $tunai;
                    $ap->kredit = $kredit;
                    $ap->sisa = $kredit;
                    $ap->tgljatuhtempo = $request->tgljatuhtempo ?: $request->tglpenerimaan;
                    $ap->save();
                }
            });

            return redirect()->route('penerimaan.show', $id)->with('success', 'Penerimaan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        if (auth()->user()->username !== 'hdy') {
            abort(403);
        }

        try {
            $penerimaan = Penerimaan::findOrFail($id);

            $ap = Ap::where('nopenerimaan', $penerimaan->nopenerimaan)->first();
            if ($ap && $ap->bayar > 0) {
                return back()->withErrors(['error' => 'Tidak bisa menghapus penerimaan yang sudah ada pembayaran hutangnya (Rp ' . number_format($ap->bayar, 0, ',', '.') . ').']);
            }

            DB::transaction(function () use ($penerimaan, $ap) {
                if ($ap) {
                    $ap->delete();
                }
                // DB triggers akan reverse stock otomatis saat detail dihapus
                Penerimaandetail::where('nopenerimaan', $penerimaan->nopenerimaan)->delete();
                $penerimaan->delete();
            });

            return redirect()->route('penerimaan.index')->with('success', 'Penerimaan ' . $penerimaan->nopenerimaan . ' berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }

    private function generateNoPenerimaan()
    {
        $prefix = 'BPE-' . date('Y');
        $last = Penerimaan::where('nopenerimaan', 'like', $prefix . '%')
            ->orderBy('nopenerimaan', 'desc')
            ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($last->nopenerimaan, -4);
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $newNumber;
    }

    public function store(Request $request)
    {
        // For brevity in high-level plan, focusing on core logic
        // In real implementation, validation should be thorough
        
        try {
            DB::transaction(function() use ($request) {
                $penerimaan = Penerimaan::create([
                    'nopenerimaan' => $this->generateNoPenerimaan(),
                    'tglpenerimaan' => $request->tglpenerimaan,
                    'supplier' => $request->supplier_id,
                    'namasupplier' => Supplier::find($request->supplier_id)?->keterangan ?? '-',
                    'totalbarang' => $request->grandtotal,
                    'totaldiskon' => 0,
                    'biayapenerimaan' => 0,
                    'grandtotal' => $request->grandtotal,
                    'pengguna' => auth()->id(),
                    'kredit' => max(0, $request->grandtotal - $request->tunai),
                    'tunai' => min($request->tunai, $request->grandtotal),
                    'tgljatuhtempo' => $request->tgljatuhtempo ?: $request->tglpenerimaan,
                    'waktu' => now(),
                ]);

                foreach ($request->items as $index => $item) {
                    $barang = Barang::with('satuanRel')->find($item['kodebarang']);
                    Penerimaandetail::create([
                        'nopenerimaan' => $penerimaan->nopenerimaan,
                        'kodebarang' => $item['kodebarang'],
                        'nourut' => $index + 1,
                        'satuan' => $barang->satuan,
                        'jumlah' => $item['jumlah'],
                        'harga' => $item['harga'],
                        'diskon' => 0,
                        'hargadiskon' => $item['harga'],
                        'subtotal' => $item['jumlah'] * $item['harga'],
                        'tglpenerimaan' => $penerimaan->tglpenerimaan,
                        'namasatuan' => $barang->satuanRel->keterangan ?? 'PCS',
                        'namabarang' => $barang->namabarang,
                    ]);
                }
            });

            return redirect()->route('penerimaan.index')->with('success', 'Penerimaan barang berhasil dicatat');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()])->withInput();
        }
    }
}
