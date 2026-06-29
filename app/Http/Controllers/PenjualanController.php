<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Penjualandetail;
use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Ar;
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

        $penjualans = $query->orderBy('tglpenjualan', 'desc')->orderBy('nopenjualan', 'desc')->paginate(20);
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

    public function edit($id)
    {
        if (auth()->user()->username !== 'hdy') {
            abort(403);
        }
        $penjualan = Penjualan::with(['details.barang.satuanRel'])->findOrFail($id);
        $pelanggans = Pelanggan::all();
        $barangs = Barang::with('stok')->get();
        return view('penjualan.edit', compact('penjualan', 'pelanggans', 'barangs'));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->username !== 'hdy') {
            abort(403);
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $penjualan = Penjualan::findOrFail($id);

                $grandtotal = $request->grandtotal;
                $tunai = min($request->tunai, $grandtotal);
                $kredit = max(0, $grandtotal - $tunai);

                $penjualan->tglpenjualan = $request->tglpenjualan;
                $penjualan->tgljatuhtempo = $request->tgljatuhtempo ?: $request->tglpenjualan;
                $penjualan->pelanggan = $request->kodepelanggan;
                $penjualan->namapelanggan = Pelanggan::find($request->kodepelanggan)?->namapelanggan ?? '-';
                $penjualan->totalbarang = $grandtotal;
                $penjualan->grandtotal = $grandtotal;
                $penjualan->tunai = $tunai;
                $penjualan->kredit = $kredit;
                $penjualan->save();

                // Delete old details; DB triggers akan mengembalikan stok otomatis
                Penjualandetail::where('nopenjualan', $penjualan->nopenjualan)->delete();

                // Insert new details; DB triggers akan mengurangi stok otomatis
                foreach ($request->items as $index => $item) {
                    $barang = Barang::with(['satuanRel', 'kemasanRel'])->find($item['kodebarang']);
                    $jumlahKemasan = ($barang->isisatuan && $barang->isisatuan > 0)
                        ? round($item['jumlah'] / $barang->isisatuan, 4)
                        : 0;

                    Penjualandetail::create([
                        'nopenjualan' => $penjualan->nopenjualan,
                        'nourut' => $index + 1,
                        'kodebarang' => $item['kodebarang'],
                        'namabarang' => $barang->namabarang,
                        'satuan' => $barang->satuan,
                        'namasatuan' => $barang->satuanRel?->keterangan ?? 'PCS',
                        'jumlah' => $item['jumlah'],
                        'harga' => $item['harga'],
                        'diskon' => 0,
                        'subtotal' => $item['jumlah'] * $item['harga'],
                        'hppsubtotal' => 0,
                        'tglpenjualan' => $penjualan->tglpenjualan,
                        'hargadiskon' => $item['harga'],
                        'kemasan' => $barang->kemasan,
                        'namakemasan' => $barang->kemasanRel?->keterangan,
                        'jumlahkemasan' => $jumlahKemasan,
                        'isisatuan' => $barang->isisatuan,
                    ]);
                }

                // Update AR jika ada dan belum ada penerimaan pembayaran
                $ar = Ar::where('nopenjualan', $penjualan->nopenjualan)->first();
                if ($ar && $ar->bayar == 0) {
                    $ar->total = $grandtotal;
                    $ar->tunai = $tunai;
                    $ar->kredit = $kredit;
                    $ar->sisa = $kredit;
                    $ar->tgljatuhtempo = $request->tgljatuhtempo ?: $request->tglpenjualan;
                    $ar->save();
                }
            });

            return redirect()->route('penjualan.show', $id)->with('success', 'Penjualan berhasil diperbarui.');
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
            $penjualan = Penjualan::findOrFail($id);

            $ar = Ar::where('nopenjualan', $penjualan->nopenjualan)->first();
            if ($ar && $ar->bayar > 0) {
                return back()->withErrors(['error' => 'Tidak bisa menghapus penjualan yang sudah ada penerimaan pembayaran piutangnya (Rp ' . number_format($ar->bayar, 0, ',', '.') . ').']);
            }

            DB::transaction(function () use ($penjualan, $ar) {
                if ($ar) {
                    $ar->delete();
                }
                // DB triggers akan mengembalikan stok otomatis saat detail dihapus
                Penjualandetail::where('nopenjualan', $penjualan->nopenjualan)->delete();
                $penjualan->delete();
            });

            return redirect()->route('penjualan.index')->with('success', 'Penjualan ' . $penjualan->nopenjualan . ' berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $user = auth()->user()->load('salesman');

        try {
            return DB::transaction(function () use ($request, $user) {
                $penjualan = Penjualan::create([
                    'nopenjualan' => $this->generateNoPenjualan(),
                    'tglpenjualan' => $request->tglpenjualan,
                    'tgljatuhtempo' => $request->tgljatuhtempo ?: $request->tglpenjualan,
                    'pelanggan' => $request->kodepelanggan,
                    'namapelanggan' => Pelanggan::find($request->kodepelanggan)?->namapelanggan ?? '-',
                    'salesman' => $user->salesman_id ?? 0,
                    'namasalesman' => $user->salesman?->keterangan ?? strtoupper($user->username),
                    'totalbarang' => $request->grandtotal,
                    'totaldiskon' => 0,
                    'grandtotal' => $request->grandtotal,
                    'hpptotal' => 0,
                    'tunai' => min($request->tunai, $request->grandtotal),
                    'kredit' => max(0, $request->grandtotal - $request->tunai),
                    'pengguna' => $user->id,
                    'shift' => 1,
                    'waktu' => now(),
                ]);

                foreach ($request->items as $index => $item) {
                    $barang = Barang::with(['satuanRel', 'kemasanRel'])->find($item['kodebarang']);
                    $jumlahKemasan = ($barang->isisatuan && $barang->isisatuan > 0)
                        ? round($item['jumlah'] / $barang->isisatuan, 4)
                        : 0;

                    Penjualandetail::create([
                        'nopenjualan' => $penjualan->nopenjualan,
                        'nourut' => $index + 1,
                        'kodebarang' => $item['kodebarang'],
                        'namabarang' => $barang->namabarang,
                        'satuan' => $barang->satuan,
                        'namasatuan' => $barang->satuanRel?->keterangan ?? 'PCS',
                        'jumlah' => $item['jumlah'],
                        'harga' => $item['harga'],
                        'diskon' => 0,
                        'subtotal' => $item['jumlah'] * $item['harga'],
                        'hppsubtotal' => 0,
                        'tglpenjualan' => $penjualan->tglpenjualan,
                        'hargadiskon' => $item['harga'],
                        'kemasan' => $barang->kemasan,
                        'namakemasan' => $barang->kemasanRel?->keterangan,
                        'jumlahkemasan' => $jumlahKemasan,
                        'isisatuan' => $barang->isisatuan,
                    ]);
                }

                return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dicatat');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()])->withInput();
        }
    }
}
