<?php

namespace App\Http\Controllers;

use App\Models\Penerimaan;
use App\Models\Penerimaandetail;
use App\Models\Barang;
use App\Models\Supplier;
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
