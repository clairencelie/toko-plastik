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
    public function stockReport()
    {
        $stocks = Mutasibarang::with('barang')->get();
        return view('reports.stock', compact('stocks'));
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
