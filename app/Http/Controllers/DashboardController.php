<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Penerimaan;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = Barang::count();
        $totalPelanggan = Pelanggan::count();
        $totalPenjualan = Penjualan::count();
        $revenue = Penjualan::sum('grandtotal');
        
        $recentSales = Penjualan::orderBy('waktu', 'desc')->limit(5)->get();

        return view('dashboard', compact(
            'totalBarang', 
            'totalPelanggan', 
            'totalPenjualan', 
            'revenue',
            'recentSales'
        ));
    }
}
