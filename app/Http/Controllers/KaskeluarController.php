<?php

namespace App\Http\Controllers;

use App\Models\Kaskeluar;
use App\Models\Ap;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KaskeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = Kaskeluar::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nokaskeluar', 'like', "%{$search}%")
                  ->orWhere('noref', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
        }

        $payments = $query->orderBy('tanggal', 'desc')->paginate(20);
        return view('kaskeluar.index', compact('payments'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $nokaskeluar = '[Otomatis]';
        return view('kaskeluar.create', compact('suppliers', 'nokaskeluar'));
    }

    public function getUnpaidAp($supplierId)
    {
        $ap = Ap::where('supplier', (int)$supplierId)
                ->where('sisa', '>', 0)
                ->get();
        return response()->json($ap);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'supplier_id' => 'required|exists:supplier,supplier',
            'items' => 'required|array|min:1',
            'items.*.noap' => 'required|exists:ap,noap',
            'items.*.bayar' => 'required|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $supplier = Supplier::find($request->supplier_id);
                $totalPayment = 0;
                $processedCount = 0;

                foreach ($request->items as $item) {
                    if ($item['bayar'] > 0) {
                        $ap = Ap::where('noap', $item['noap'])->lockForUpdate()->first();
                        
                        if (!$ap) continue;

                        $paymentAmount = min($item['bayar'], $ap->sisa);
                        
                        if ($paymentAmount <= 0) continue;

                        $nokaskeluar = $this->generateNoKaskeluar();
                        
                        $kaskeluar = new Kaskeluar();
                        $kaskeluar->nokaskeluar = $nokaskeluar;
                        $kaskeluar->tanggal = $request->tanggal;
                        $kaskeluar->noref = $ap->noap;
                        $kaskeluar->keterangan = $request->keterangan ?? "Pelunasan Hutang {$ap->noap}";
                        $kaskeluar->jumlah = $paymentAmount;
                        $kaskeluar->nama = $supplier->keterangan;
                        $kaskeluar->langsung = false;
                        $kaskeluar->save();

                        // Update AP
                        $ap->bayar += $paymentAmount;
                        $ap->sisa -= $paymentAmount;
                        $ap->save();

                        $totalPayment += $paymentAmount;
                        $processedCount++;
                    }
                }

                if ($totalPayment <= 0) {
                    throw new \Exception('Jumlah pembayaran harus lebih dari 0');
                }

                return redirect()->route('kaskeluar.index')->with('success', 'Pelunasan hutang berhasil dicatat');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()])->withInput();
        }
    }

    private function generateNoKaskeluar()
    {
        $result = DB::select('SELECT buatnumeratortransaksi(8) as new_id');
        return $result[0]->new_id;
    }
}
