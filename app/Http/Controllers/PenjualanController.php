<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Penjualandetail;
use App\Models\Barang;
use App\Models\Pelanggan;
use App\Services\InventoryService;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    protected $inventoryService;
    protected $financeService;

    public function __construct(InventoryService $inventoryService, FinanceService $financeService)
    {
        $this->inventoryService = $inventoryService;
        $this->financeService = $financeService;
    }

    public function index()
    {
        $penjualans = Penjualan::with('pelangganRel')->paginate(20);
        return view('penjualan.index', compact('penjualans'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::all();
        $barangs = Barang::all();
        return view('penjualan.create', compact('pelanggans', 'barangs'));
    }

    public function store(Request $request)
    {
        return DB::transaction(function() use ($request) {
            $penjualan = Penjualan::create([
                'nopenjualan' => $request->nopenjualan,
                'tglpenjualan' => $request->tglpenjualan,
                'tgljatuhtempo' => $request->tgljatuhtempo,
                'pelanggan' => $request->pelanggan_id,
                'namapelanggan' => Pelanggan::find($request->pelanggan_id)->namapelanggan,
                'salesman' => 0, // Default for now
                'namasalesman' => 'ADMIN',
                'totalbarang' => $request->grandtotal,
                'totaldiskon' => 0,
                'grandtotal' => $request->grandtotal,
                'hpptotal' => 0, // Will be updated after details
                'tunai' => min($request->tunai, $request->grandtotal),
                'kredit' => max(0, $request->grandtotal - $request->tunai),
                'pengguna' => 1,
                'shift' => 1,
                'waktu' => now(),
            ]);

            foreach ($request->items as $index => $item) {
                // 1. Calculate HPP using FIFO via InventoryService
                $totalHPP = $this->inventoryService->reduceStock(
                    $penjualan->nopenjualan,
                    $item['kodebarang'],
                    $index + 1,
                    $item['jumlah'],
                    $penjualan->tglpenjualan
                );

                // 2. Create detail record
                Penjualandetail::create([
                    'nopenjualan' => $penjualan->nopenjualan,
                    'nourut' => $index + 1,
                    'kodebarang' => $item['kodebarang'],
                    'namabarang' => Barang::find($item['kodebarang'])->namabarang,
                    'satuan' => $item['satuan'],
                    'namasatuan' => 'PCS',
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'diskon' => 0,
                    'subtotal' => $item['jumlah'] * $item['harga'],
                    'hppsubtotal' => $totalHPP,
                    'tglpenjualan' => $penjualan->tglpenjualan,
                    'hargadiskon' => $item['harga'],
                ]);
            }

            // 3. Handle Accounts Receivable (Piutang) if not fully paid
            if ($penjualan->kredit > 0) {
                $this->financeService->createAR(
                    'AR-' . $penjualan->nopenjualan,
                    $penjualan->tglpenjualan,
                    $penjualan->nopenjualan,
                    $penjualan->pelanggan,
                    $penjualan->grandtotal,
                    $penjualan->tunai,
                    $penjualan->kredit,
                    $penjualan->tgljatuhtempo
                );
            }

            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dicatat');
        });
    }
}
