<?php

namespace App\Http\Controllers;

use App\Models\Kasmasuk;
use App\Models\Ar;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KasmasukController extends Controller
{
    public function index(Request $request)
    {
        $query = Kasmasuk::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nokasmasuk', 'like', "%{$search}%")
                  ->orWhere('noref', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
        }

        $payments = $query->orderBy('tanggal', 'desc')->paginate(20);
        return view('kasmasuk.index', compact('payments'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::all();
        $nokasmasuk = '[Otomatis]';
        return view('kasmasuk.create', compact('pelanggans', 'nokasmasuk'));
    }

    public function getUnpaidAr($customerId)
    {
        $ar = Ar::where('pelanggan', (int)$customerId)
                ->where('sisa', '>', 0)
                ->get();
        return response()->json($ar);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kodepelanggan' => 'required|exists:pelanggan,kodepelanggan',
            'items' => 'required|array|min:1',
            'items.*.noar' => 'required|exists:ar,noar',
            'items.*.bayar' => 'required|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $pelanggan = Pelanggan::find($request->kodepelanggan);
                $totalPayment = 0;
                $processedCount = 0;

                foreach ($request->items as $item) {
                    if ($item['bayar'] > 0) {
                        $ar = Ar::where('noar', $item['noar'])->lockForUpdate()->first();
                        
                        if (!$ar) continue;

                        $paymentAmount = min($item['bayar'], $ar->sisa);
                        
                        if ($paymentAmount <= 0) continue;

                        $nokasmasuk = $this->generateNoKasmasuk();
                        
                        $kasmasuk = new Kasmasuk();
                        $kasmasuk->nokasmasuk = $nokasmasuk;
                        $kasmasuk->tanggal = $request->tanggal;
                        $kasmasuk->noref = $ar->noar;
                        $kasmasuk->keterangan = $request->keterangan ?? "Pelunasan Piutang {$ar->noar}";
                        $kasmasuk->jumlah = $paymentAmount;
                        $kasmasuk->nama = $pelanggan->namapelanggan;
                        $kasmasuk->langsung = false;
                        $kasmasuk->save();

                        // Update AR
                        $ar->bayar += $paymentAmount;
                        $ar->sisa -= $paymentAmount;
                        $ar->save();

                        $totalPayment += $paymentAmount;
                        $processedCount++;
                    }
                }

                if ($totalPayment <= 0) {
                    throw new \Exception('Jumlah pembayaran harus lebih dari 0');
                }

                return redirect()->route('kasmasuk.index')->with('success', 'Pelunasan piutang berhasil dicatat');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()])->withInput();
        }
    }

    private function generateNoKasmasuk()
    {
        $result = DB::select('SELECT buatnumeratortransaksi(7) as new_id');
        return $result[0]->new_id;
    }
}
