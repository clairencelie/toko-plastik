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
    protected $inventoryService;
    protected $financeService;

    public function __construct(InventoryService $inventoryService, FinanceService $financeService)
    {
        $this->inventoryService = $inventoryService;
        $this->financeService = $financeService;
    }

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

        $penerimaans = $query->orderBy('tglpenerimaan', 'desc')->paginate(20);
        return view('penerimaan.index', compact('penerimaans'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $barangs = Barang::all();
        return view('penerimaan.create', compact('suppliers', 'barangs'));
    }

    public function store(Request $request)
    {
        // For brevity in high-level plan, focusing on core logic
        // In real implementation, validation should be thorough
        
        DB::transaction(function() use ($request) {
            $penerimaan = Penerimaan::create([
                'nopenerimaan' => $request->nopenerimaan,
                'tglpenerimaan' => $request->tglpenerimaan,
                'supplier' => $request->supplier_id,
                'namasupplier' => Supplier::find($request->supplier_id)->keterangan,
                'totalbarang' => $request->grandtotal, // Total before discount
                'totaldiskon' => 0,
                'biayapenerimaan' => 0,
                'grandtotal' => $request->grandtotal,
                'pengguna' => 1,
                'kredit' => $request->grandtotal - $request->tunai,
                'tunai' => $request->tunai,
                'tgljatuhtempo' => $request->tgljatuhtempo,
                'waktu' => now(),
            ]);

            foreach ($request->items as $index => $item) {
                Penerimaandetail::create([
                    'nopenerimaan' => $penerimaan->nopenerimaan,
                    'kodebarang' => $item['kodebarang'],
                    'nourut' => $index + 1,
                    'satuan' => $item['satuan'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'diskon' => 0,
                    'hargadiskon' => $item['harga'],
                    'subtotal' => $item['jumlah'] * $item['harga'],
                    'tglpenerimaan' => $penerimaan->tglpenerimaan,
                    'namasatuan' => 'PCS',
                    'namabarang' => Barang::find($item['kodebarang'])->namabarang,
                ]);

                // Update Stock via InventoryService
                $this->inventoryService->addStock(
                    $penerimaan->nopenerimaan,
                    $item['kodebarang'],
                    $index + 1,
                    $item['jumlah'],
                    $item['harga'],
                    $penerimaan->tglpenerimaan
                );
            }

            // Handle Accounts Payable (Hutang)
            if ($penerimaan->kredit > 0) {
                $this->financeService->createAP(
                    'AP-' . $penerimaan->nopenerimaan,
                    $penerimaan->tglpenerimaan,
                    $penerimaan->nopenerimaan,
                    $penerimaan->supplier,
                    $penerimaan->grandtotal,
                    $penerimaan->tunai,
                    $penerimaan->kredit,
                    $penerimaan->tgljatuhtempo
                );
            }
        });

        return redirect()->route('penerimaan.index')->with('success', 'Penerimaan barang berhasil dicatat');
    }
}
