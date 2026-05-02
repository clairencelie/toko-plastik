<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Mutasibarang;
use App\Models\Ap;
use App\Models\Ar;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function stockReport(Request $request)
    {
        $query = Mutasibarang::with('barang');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kodebarang', 'like', "%{$search}%")
                  ->orWhereHas('barang', function($bq) use ($search) {
                      $bq->where('namabarang', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status == 'low') {
                $query->whereHas('barang', function($bq) {
                    $bq->whereRaw('mutasibarang.saldoakhir <= barang.stokminimal')
                       ->where('mutasibarang.saldoakhir', '>', 0);
                });
            } elseif ($status == 'out') {
                $query->where('saldoakhir', '<=', 0);
            }
        }

        $stocks = $query->paginate(20);
        
        // For summary cards, we might still need total counts across all records
        // but for high-level high-performance, we can calculate these separately or just use totals from the current view/pagination if acceptable.
        // The user asked not to pull all data, so I'll count specific statuses.
        $totalItems = Mutasibarang::count();
        $lowStockCount = Mutasibarang::whereHas('barang', function($bq) {
            $bq->whereRaw('mutasibarang.saldoakhir <= barang.stokminimal')
               ->where('mutasibarang.saldoakhir', '>', 0);
        })->count();
        $outOfStockCount = Mutasibarang::where('saldoakhir', '<=', 0)->count();

        return view('reports.stock', compact('stocks', 'totalItems', 'lowStockCount', 'outOfStockCount'));
    }

    public function financialReport()
    {
        $totalHutang = Ap::sum('sisa');
        $totalPiutang = Ar::sum('sisa');
        
        // Simple Profit calculation based on HPP recorded in Penjualandetail
        $salesProfit = DB::table('penjualandetail')
            ->select(DB::raw('SUM(subtotal) as revenue'), DB::raw('SUM(hppsubtotal) as total_hpp'))
            ->first();
            
        return view('reports.financial', compact('totalHutang', 'totalPiutang', 'salesProfit'));
    }
}
