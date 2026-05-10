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
    public function index(Request $request)
    {
        $query = Penjualan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nopenjualan', 'like', "%{$search}%")
                    ->orWhere('namapelanggan', 'like', "%{$search}%");
            });
        }

        $penjualans = $query->orderBy('tglpenjualan', 'desc')->paginate(20);
        return view('penjualan.index', compact('penjualans'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::all();
        $barangs = Barang::with('stok')->get();
        $nopenjualan = $this->generateNoPenjualan();
        return view('penjualan.create', compact('pelanggans', 'barangs', 'nopenjualan'));
    }

    public function show($id)
    {
        $penjualan = Penjualan::with(['details', 'pelangganRel'])->findOrFail($id);
        return view('penjualan.show', compact('penjualan'));
    }

    public function printInvoice($id)
    {
        $penjualan = Penjualan::with(['details', 'pelangganRel'])->findOrFail($id);
        return view('penjualan.invoice', compact('penjualan'));
    }

    private function generateNoPenjualan()
    {
        $prefix = 'SP-' . date('Y');
        $last = Penjualan::where('nopenjualan', 'like', $prefix . '%')
            ->orderBy('nopenjualan', 'desc')
            ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($last->nopenjualan, -4);
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $newNumber;
    }

    public function store(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $penjualan = Penjualan::create([
                    'nopenjualan' => $this->generateNoPenjualan(),
                    'tglpenjualan' => $request->tglpenjualan,
                    'tgljatuhtempo' => $request->tgljatuhtempo ?: $request->tglpenjualan,
                    'pelanggan' => $request->kodepelanggan,
                    'namapelanggan' => Pelanggan::find($request->kodepelanggan)->namapelanggan ?? '-',
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
                    $barang = Barang::with('satuanRel')->find($item['kodebarang']);
                    Penjualandetail::create([
                        'nopenjualan' => $penjualan->nopenjualan,
                        'nourut' => $index + 1,
                        'kodebarang' => $item['kodebarang'],
                        'namabarang' => $barang->namabarang,
                        'satuan' => $barang->satuan,
                        'namasatuan' => $barang->satuanRel->keterangan ?? 'PCS',
                        'jumlah' => $item['jumlah'],
                        'harga' => $item['harga'],
                        'diskon' => 0,
                        'subtotal' => $item['jumlah'] * $item['harga'],
                        'hppsubtotal' => 0, // DB trigger will calculate this
                        'tglpenjualan' => $penjualan->tglpenjualan,
                        'hargadiskon' => $item['harga'],
                    ]);
                }

                return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dicatat');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()])->withInput();
        }
    }
}
