<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Tagihandetail;
use App\Models\Ar;
use App\Models\Pelanggan;
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
        return view('tagihan.create', compact('pelanggans', 'notagihan'));
    }

    public function getUnpaidAr($customerId)
    {
        $ar = Ar::where('pelanggan', (int)$customerId)
                ->where('sisa', '>', 0)
                ->get();
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
        return view('tagihan.print', compact('tagihan'));
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

                $tagihan = Tagihan::create([
                    'notagihan' => $notagihan,
                    'tgltagihan' => $request->tgltagihan,
                    'keterangan' => $request->keterangan ?? '-',
                    'grandtotal' => $grandtotal,
                    'totalbayar' => 0,
                ]);

                $nourut = 1;
                foreach ($selectedItems as $item) {
                    Tagihandetail::create([
                        'notagihan' => $tagihan->notagihan,
                        'nopenjualan' => $item['nopenjualan'],
                        'nourut' => $nourut++,
                        'nama' => $request->namapelanggan ?? '-',
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
