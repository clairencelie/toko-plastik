<?php

namespace App\Services;

use App\Models\Ap;
use App\Models\Ar;
use App\Models\Kaskeluar;
use App\Models\Kasmasuk;
use App\Models\Supplier;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    /**
     * Create Account Payable (AP) record.
     */
    public function createAP($noAP, $tglAP, $noPenerimaan, $supplierId, $total, $tunai, $kredit, $tglJatuhTempo)
    {
        return DB::transaction(function () use ($noAP, $tglAP, $noPenerimaan, $supplierId, $total, $tunai, $kredit, $tglJatuhTempo) {
            $supplier = Supplier::findOrFail($supplierId);

            $ap = Ap::create([
                'noap' => $noAP,
                'tglap' => $tglAP,
                'nopenerimaan' => $noPenerimaan,
                'supplier' => $supplierId,
                'namasupplier' => $supplier->keterangan,
                'tgljatuhtempo' => $tglJatuhTempo,
                'total' => $total,
                'tunai' => $tunai,
                'kredit' => $kredit,
                'bayar' => 0,
                'sisa' => $kredit,
                'retur' => 0
            ]);

            // Update supplier total hutang
            $supplier->hutang += $kredit;
            $supplier->save();

            return $ap;
        });
    }

    /**
     * Create Account Receivable (AR) record.
     */
    public function createAR($noAR, $tglAR, $noPenjualan, $pelangganId, $total, $tunai, $kredit, $tglJatuhTempo)
    {
        return DB::transaction(function () use ($noAR, $tglAR, $noPenjualan, $pelangganId, $total, $tunai, $kredit, $tglJatuhTempo) {
            $pelanggan = Pelanggan::findOrFail($pelangganId);

            $ar = Ar::create([
                'noar' => $noAR,
                'tglar' => $tglAR,
                'nopenjualan' => $noPenjualan,
                'pelanggan' => $pelangganId,
                'namapelanggan' => $pelanggan->namapelanggan,
                'tgljatuhtempo' => $tglJatuhTempo,
                'total' => $total,
                'tunai' => $tunai,
                'kredit' => $kredit,
                'bayar' => 0,
                'sisa' => $kredit
            ]);

            // Update pelanggan total piutang
            $pelanggan->piutang += $kredit;
            $pelanggan->save();

            return $ar;
        });
    }

    /**
     * Record payment for AP (Kas Keluar).
     */
    public function payAP($noKasKeluar, $tgl, $noAP, $jumlah, $keterangan, $penggunaId)
    {
        return DB::transaction(function () use ($noKasKeluar, $tgl, $noAP, $jumlah, $keterangan, $penggunaId) {
            $ap = Ap::findOrFail($noAP);
            
            Kaskeluar::create([
                'nokaskeluar' => $noKasKeluar,
                'tanggal' => $tgl,
                'noref' => $noAP,
                'keterangan' => $keterangan,
                'jumlah' => $jumlah,
                'nama' => $ap->namasupplier,
                'langsung' => false
            ]);

            $ap->bayar += $jumlah;
            $ap->sisa -= $jumlah;
            $ap->save();

            $supplier = Supplier::findOrFail($ap->supplier);
            $supplier->hutang -= $jumlah;
            $supplier->save();

            return true;
        });
    }

    /**
     * Record receipt for AR (Kas Masuk).
     */
    public function receiveAR($noKasMasuk, $tgl, $noAR, $jumlah, $keterangan, $penggunaId)
    {
        return DB::transaction(function () use ($noKasMasuk, $tgl, $noAR, $jumlah, $keterangan, $penggunaId) {
            $ar = Ar::findOrFail($noAR);
            
            Kasmasuk::create([
                'nokasmasuk' => $noKasMasuk,
                'tanggal' => $tgl,
                'noref' => $noAR,
                'keterangan' => $keterangan,
                'jumlah' => $jumlah,
                'nama' => $ar->namapelanggan,
                'langsung' => false
            ]);

            $ar->bayar += $jumlah;
            $ar->sisa -= $jumlah;
            $ar->save();

            $pelanggan = Pelanggan::findOrFail($ar->pelanggan);
            $pelanggan->piutang -= $jumlah;
            $pelanggan->save();

            return true;
        });
    }
}
