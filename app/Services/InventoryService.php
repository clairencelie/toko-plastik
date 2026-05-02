<?php

namespace App\Services;

use App\Models\Fifostock;
use App\Models\Mutasibarang;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Add stock using FIFO method.
     * This replaces the logic in triggers like adjustmendetail_on_insert for positive adjustments
     * and goods receipt (penerimaan).
     */
    public function addStock($noBukti, $kodeBarang, $noUrut, $jumlah, $harga, $waktu = null)
    {
        $waktu = $waktu ?: now();

        return DB::transaction(function () use ($noBukti, $kodeBarang, $noUrut, $jumlah, $harga, $waktu) {
            // 1. Insert into fifostock
            Fifostock::create([
                'nobukti' => $noBukti,
                'kodebarang' => $kodeBarang,
                'nourut' => $noUrut,
                'waktu' => $waktu,
                'harga' => $harga,
                'masuk' => $jumlah,
                'keluar' => 0,
                'saldo' => $jumlah,
                'returmasuk' => 0,
                'returkeluar' => 0,
                'hargabonus' => 0
            ]);

            // 2. Update mutasibarang
            $mutasi = Mutasibarang::where('kodebarang', $kodeBarang)->first();
            if (!$mutasi) {
                $mutasi = Mutasibarang::create(['kodebarang' => $kodeBarang]);
            }

            // Logic matches schema.sql: (saldoawal+beli+returkeluar+returjual+rakit)-(returbeli+keluar+jual)+adjustmen
            // Here we assume this addition is from 'beli' or 'adjustmen' based on context, 
            // but for simplicity we update 'beli' for now.
            // In a real scenario, we might pass the transaction type.
            $mutasi->beli += $jumlah;
            $mutasi->saldoakhir += $jumlah;
            $mutasi->save();

            return true;
        });
    }

    /**
     * Reduce stock using FIFO method and calculate HPP.
     * This replaces the core logic of carihpp function in schema.sql.
     */
    public function reduceStock($noTransaksi, $kodeBarang, $noUrutTrans, $jumlahRequested, $waktuTrans = null)
    {
        $waktuTrans = $waktuTrans ?: now();
        $totalHPP = 0;
        $remainingToReduce = $jumlahRequested;

        return DB::transaction(function () use ($noTransaksi, $kodeBarang, $noUrutTrans, $remainingToReduce, $waktuTrans, &$totalHPP) {
            // Check total available stock
            $availableStock = Fifostock::where('kodebarang', $kodeBarang)->sum('saldo');
            if ($availableStock < $remainingToReduce) {
                $barang = Barang::find($kodeBarang);
                throw new Exception("Saldo tidak mencukupi untuk barang: " . ($barang ? $barang->namabarang : $kodeBarang));
            }

            // Get FIFO batches (oldest first)
            $batches = Fifostock::where('kodebarang', $kodeBarang)
                ->where('saldo', '>', 0)
                ->orderBy('waktu')
                ->orderBy('nobukti')
                ->orderBy('nourut')
                ->get();

            foreach ($batches as $batch) {
                if ($remainingToReduce <= 0) break;

                $reduceAmount = min($batch->saldo, $remainingToReduce);
                
                // Calculate HPP for this batch
                $totalHPP += ($reduceAmount * $batch->harga);

                // Update the batch
                $batch->keluar += $reduceAmount;
                $batch->saldo = ($batch->masuk + $batch->returkeluar) - ($batch->keluar + $batch->returmasuk);
                $batch->save();

                // Logic for fifostocktransaksi would go here if we implement that table

                $remainingToReduce -= $reduceAmount;
            }

            // Update mutasibarang
            $mutasi = Mutasibarang::where('kodebarang', $kodeBarang)->first();
            if ($mutasi) {
                $mutasi->jual += $jumlahRequested;
                $mutasi->saldoakhir -= $jumlahRequested;
                $mutasi->save();
            }

            return $totalHPP;
        });
    }
}
