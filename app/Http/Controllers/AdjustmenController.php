<?php

namespace App\Http\Controllers;

use App\Models\Adjustmen;
use App\Models\Adjustmendetail;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdjustmenController extends Controller
{
    public function index(Request $request)
    {
        $query = Adjustmen::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('noadjustmen', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
        }

        $adjustmens = $query->orderBy('tanggaladjustmen', 'desc')->paginate(20);
        return view('adjustmen.index', compact('adjustmens'));
    }

    public function create()
    {
        $barangs = Barang::with('stok')->get();
        $noadjustmen = $this->generateNoAdjustmen();
        return view('adjustmen.create', compact('barangs', 'noadjustmen'));
    }

    public function show($id)
    {
        $adjustmen = Adjustmen::with('details.barang')->findOrFail($id);
        return view('adjustmen.show', compact('adjustmen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggaladjustmen' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.kodebarang' => 'required|exists:barang,kodebarang',
            'items.*.jumlah' => 'required|numeric',
            'items.*.harga' => 'required|numeric',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $noadjustmen = $this->generateNoAdjustmen();
                
                $adjustpositif = 0;
                $adjustnegatif = 0;
                $grandtotal = 0;

                foreach ($request->items as $item) {
                    $subtotal = $item['jumlah'] * $item['harga'];
                    $grandtotal += $subtotal;
                    if ($item['jumlah'] > 0) {
                        $adjustpositif += $subtotal;
                    } else {
                        $adjustnegatif += abs($subtotal);
                    }
                }

                $adjustmen = Adjustmen::create([
                    'noadjustmen' => $noadjustmen,
                    'keterangan' => $request->keterangan ?? '-',
                    'tanggaladjustmen' => $request->tanggaladjustmen,
                    'grandtotal' => $grandtotal,
                    'pengguna' => Auth::id() ?? 1,
                    'shift' => 1,
                    'waktu' => now(),
                    'adjustpositif' => $adjustpositif,
                    'adjustnegatif' => $adjustnegatif,
                ]);

                foreach ($request->items as $index => $item) {
                    $barang = Barang::with('satuanRel')->find($item['kodebarang']);
                    Adjustmendetail::create([
                        'noadjustmen' => $adjustmen->noadjustmen,
                        'kodebarang' => $item['kodebarang'],
                        'nourut' => $index + 1,
                        'satuan' => $barang->satuan,
                        'stockkomputer' => $item['stockkomputer'] ?? 0,
                        'stockfisik' => $item['stockfisik'] ?? 0,
                        'jumlah' => $item['jumlah'],
                        'harga' => $item['harga'],
                        'subtotal' => $item['jumlah'] * $item['harga'],
                        'namasatuan' => $barang->satuanRel->keterangan ?? 'PCS',
                        'namabarang' => $barang->namabarang,
                        'tanggaladjustmen' => $adjustmen->tanggaladjustmen,
                    ]);
                }

                return redirect()->route('adjustmen.index')->with('success', 'Adjustment stok berhasil dicatat');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()])->withInput();
        }
    }

    private function generateNoAdjustmen()
    {
        $prefix = 'ADJ-' . date('Y');
        $last = Adjustmen::where('noadjustmen', 'like', $prefix . '%')
            ->orderBy('noadjustmen', 'desc')
            ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($last->noadjustmen, -4);
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $newNumber;
    }
}
