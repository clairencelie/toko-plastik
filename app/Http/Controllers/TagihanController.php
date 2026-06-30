<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Tagihandetail;
use App\Models\Ar;
use App\Models\Pelanggan;
use App\Models\Salesman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $query = Tagihan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('notagihan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
        }

        $tagihans = $query->orderBy('tgltagihan', 'desc')->paginate(20);
        return view('tagihan.index', compact('tagihans'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::all();
        $notagihan = $this->generateNoTagihan();
        $salesmen = auth()->user()->username === 'hdy' ? Salesman::all() : collect();
        return view('tagihan.create', compact('pelanggans', 'notagihan', 'salesmen'));
    }

    public function getUnpaidAr($customerId)
    {
        $ar = Ar::where('pelanggan', (int)$customerId)
                ->where('sisa', '>', 0)
                ->get();
        return response()->json($ar);
    }

    public function getUnpaidArBatch(Request $request)
    {
        $customerIds = $request->input('customers', []);

        if (empty($customerIds)) {
            return response()->json([]);
        }

        $ar = Ar::with('pelangganRel')
                ->whereIn('pelanggan', array_map('intval', (array) $customerIds))
                ->where('sisa', '>', 0)
                ->get()
                ->map(fn($item) => [
                    'nopenjualan'   => $item->nopenjualan,
                    'tglar'         => $item->tglar,
                    'tgljatuhtempo' => $item->tgljatuhtempo,
                    'sisa'          => $item->sisa,
                    'total'         => $item->total,
                    'tunai'         => $item->tunai,
                    'kredit'        => $item->kredit,
                    'bayar'         => $item->bayar,
                    'namapelanggan' => $item->pelangganRel->namapelanggan ?? '-',
                    'kodepelanggan' => $item->pelanggan,
                ]);

        return response()->json($ar);
    }

    public function show($id)
    {
        $tagihan = Tagihan::with('details')->findOrFail($id);
        return view('tagihan.show', compact('tagihan'));
    }

    public function print($id)
    {
        $tagihan = Tagihan::with('details')->findOrFail($id);
        $noPenjualans = $tagihan->details->pluck('nopenjualan');
        $arMap = Ar::whereIn('nopenjualan', $noPenjualans)->get()->keyBy('nopenjualan');
        return view('tagihan.print', compact('tagihan', 'arMap'));
    }

    public function edit($id)
    {
        if (auth()->user()->username !== 'hdy') {
            abort(403);
        }

        $tagihan = Tagihan::with('details')->findOrFail($id);
        $pelanggans = Pelanggan::all();
        $salesmen = Salesman::all();
        $arMap = Ar::whereIn('nopenjualan', $tagihan->details->pluck('nopenjualan'))->get()->keyBy('nopenjualan');

        // Kelompokkan detail yang sudah ada per pelanggan, supaya tabel piutang
        // di form edit bisa pra-terisi (sudah dicentang) seperti saat membuat baru
        $existingGroups = $tagihan->details
            ->groupBy(function ($detail) use ($arMap) {
                return $arMap[$detail->nopenjualan]->pelanggan ?? ('legacy-' . md5($detail->nama));
            })
            ->map(function ($rows, $customerId) use ($arMap) {
                return [
                    'customerId'   => $customerId,
                    'customerName' => $rows->first()->nama ?? '-',
                    'rows' => $rows->map(function ($detail) use ($arMap) {
                        $ar = $arMap[$detail->nopenjualan] ?? null;
                        return [
                            'nopenjualan'   => $detail->nopenjualan,
                            'total'         => $detail->total,
                            'tunai'         => $detail->tunai,
                            'kredit'        => $detail->kredit,
                            'bayar'         => $detail->sudahbayar,
                            'sisabayar'     => $detail->sisabayar,
                            'tglar'         => $ar->tglar ?? null,
                            'tgljatuhtempo' => $ar->tgljatuhtempo ?? null,
                        ];
                    })->values(),
                ];
            })
            ->values();

        return view('tagihan.edit', compact('tagihan', 'pelanggans', 'salesmen', 'existingGroups'));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->username !== 'hdy') {
            abort(403);
        }

        $request->validate([
            'tgltagihan' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $tagihan = Tagihan::findOrFail($id);

                $grandtotal = 0;
                $selectedItems = [];

                foreach ($request->items as $item) {
                    if (isset($item['selected']) && $item['selected'] == '1') {
                        $grandtotal += $item['sisabayar'];
                        $selectedItems[] = $item;
                    }
                }

                if (empty($selectedItems)) {
                    return back()->withErrors(['items' => 'Pilih setidaknya satu transaksi untuk ditagih'])->withInput();
                }

                $salesman = null;
                $namasalesman = null;
                if ($request->filled('salesman')) {
                    $salesmanModel = Salesman::find($request->salesman);
                    $salesman = $salesmanModel?->salesman;
                    $namasalesman = $salesmanModel?->keterangan;
                }

                $tagihan->tgltagihan = $request->tgltagihan;
                $tagihan->keterangan = $request->keterangan ?? '-';
                $tagihan->grandtotal = $grandtotal;
                $tagihan->salesman = $salesman;
                $tagihan->namasalesman = $namasalesman;
                $tagihan->save();

                Tagihandetail::where('notagihan', $tagihan->notagihan)->delete();

                $nourut = 1;
                foreach ($selectedItems as $item) {
                    Tagihandetail::create([
                        'notagihan' => $tagihan->notagihan,
                        'nopenjualan' => $item['nopenjualan'],
                        'nourut' => $nourut++,
                        'nama' => $item['namapelanggan'] ?? '-',
                        'total' => $item['total'],
                        'tunai' => $item['tunai'],
                        'kredit' => $item['kredit'],
                        'sudahbayar' => $item['bayar'],
                        'sisabayar' => $item['sisabayar'],
                        'keterangan' => '-',
                        'bayarsekarang' => 0,
                        'langsung' => false,
                        'tgltagihan' => $tagihan->tgltagihan,
                    ]);
                }

                return redirect()->route('tagihan.show', $tagihan->notagihan)->with('success', 'Tagihan berhasil diperbarui.');
            });
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
            $tagihan = Tagihan::findOrFail($id);

            if ($tagihan->totalbayar > 0) {
                return back()->withErrors(['error' => 'Tidak bisa menghapus tagihan yang sudah ada pembayarannya (Rp ' . number_format($tagihan->totalbayar, 0, ',', '.') . ').']);
            }

            DB::transaction(function () use ($tagihan) {
                Tagihandetail::where('notagihan', $tagihan->notagihan)->delete();
                $tagihan->delete();
            });

            return redirect()->route('tagihan.index')->with('success', 'Tagihan ' . $tagihan->notagihan . ' berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgltagihan' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $notagihan = $this->generateNoTagihan();
                
                $grandtotal = 0;
                $selectedItems = [];

                foreach ($request->items as $item) {
                    if (isset($item['selected']) && $item['selected'] == '1') {
                        $grandtotal += $item['sisabayar'];
                        $selectedItems[] = $item;
                    }
                }

                if (empty($selectedItems)) {
                    return back()->withErrors(['items' => 'Pilih setidaknya satu transaksi untuk ditagih'])->withInput();
                }

                $salesman = null;
                $namasalesman = null;
                if (auth()->user()->username === 'hdy' && $request->filled('salesman')) {
                    $salesmanModel = Salesman::find($request->salesman);
                    $salesman = $salesmanModel?->salesman;
                    $namasalesman = $salesmanModel?->keterangan;
                }

                $tagihan = Tagihan::create([
                    'notagihan' => $notagihan,
                    'tgltagihan' => $request->tgltagihan,
                    'keterangan' => $request->keterangan ?? '-',
                    'grandtotal' => $grandtotal,
                    'totalbayar' => 0,
                    'salesman' => $salesman,
                    'namasalesman' => $namasalesman,
                ]);

                $nourut = 1;
                foreach ($selectedItems as $item) {
                    Tagihandetail::create([
                        'notagihan' => $tagihan->notagihan,
                        'nopenjualan' => $item['nopenjualan'],
                        'nourut' => $nourut++,
                        'nama' => $item['namapelanggan'] ?? $request->namapelanggan ?? '-',
                        'total' => $item['total'],
                        'tunai' => $item['tunai'],
                        'kredit' => $item['kredit'],
                        'sudahbayar' => $item['bayar'],
                        'sisabayar' => $item['sisabayar'],
                        'keterangan' => '-',
                        'bayarsekarang' => 0,
                        'langsung' => false,
                        'tgltagihan' => $tagihan->tgltagihan,
                    ]);
                }

                return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil dibuat');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()])->withInput();
        }
    }

    private function generateNoTagihan()
    {
        $prefix = 'INV-' . date('Y');
        $last = Tagihan::where('notagihan', 'like', $prefix . '%')
            ->orderBy('notagihan', 'desc')
            ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($last->notagihan, -4);
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $newNumber;
    }
}
