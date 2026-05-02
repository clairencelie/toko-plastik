<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('penerimaan', function (Blueprint $table) {
            $table->string('nopenerimaan', 20)->primary();
            $table->date('tglpenerimaan');
            $table->integer('supplier');
            $table->string('namasupplier', 30);
            $table->date('tgljatuhtempo')->nullable();
            $table->decimal('totalbarang', 11, 2);
            $table->decimal('totaldiskon', 11, 2)->default(0);
            $table->decimal('biayapenerimaan', 11, 2)->default(0);
            $table->decimal('grandtotal', 11, 2);
            $table->decimal('tunai', 11, 2)->default(0);
            $table->decimal('kredit', 11, 2)->default(0);
            $table->integer('pengguna')->nullable();
            $table->integer('shift')->nullable();
            $table->timestamp('waktu')->nullable();
            $table->string('nofaktur', 50)->nullable();

            $table->foreign('supplier')->references('supplier')->on('supplier');
        });

        Schema::create('penerimaandetail', function (Blueprint $table) {
            $table->string('nopenerimaan', 20);
            $table->integer('nourut');
            $table->integer('kodebarang');
            $table->string('namabarang', 100);
            $table->integer('satuan');
            $table->decimal('jumlah', 7, 2);
            $table->decimal('harga', 11, 2);
            $table->decimal('diskon', 11, 2)->default(0);
            $table->decimal('hargadiskon', 11, 2);
            $table->decimal('subtotal', 11, 2);
            $table->decimal('hargaterakhir', 11, 2)->nullable();
            $table->boolean('rubah')->nullable();
            $table->date('tglpenerimaan');
            $table->string('namasatuan', 30)->nullable();

            $table->primary(['nopenerimaan', 'nourut']);
            $table->foreign('nopenerimaan')->references('nopenerimaan')->on('penerimaan');
            $table->foreign('kodebarang')->references('kodebarang')->on('barang');
        });

        Schema::create('penjualan', function (Blueprint $table) {
            $table->string('nopenjualan', 20)->primary();
            $table->date('tglpenjualan');
            $table->date('tgljatuhtempo')->nullable();
            $table->integer('pelanggan');
            $table->string('namapelanggan', 50);
            $table->integer('salesman');
            $table->string('namasalesman', 30);
            $table->decimal('totalbarang', 11, 2);
            $table->decimal('totaldiskon', 11, 2)->default(0);
            $table->decimal('grandtotal', 11, 2);
            $table->decimal('hpptotal', 11, 2)->nullable();
            $table->decimal('tunai', 11, 2)->default(0);
            $table->decimal('kredit', 11, 2)->default(0);
            $table->integer('pengguna')->nullable();
            $table->integer('shift')->nullable();
            $table->timestamp('waktu')->nullable();

            $table->foreign('pelanggan')->references('kodepelanggan')->on('pelanggan');
        });

        Schema::create('penjualandetail', function (Blueprint $table) {
            $table->string('nopenjualan', 20);
            $table->integer('nourut');
            $table->integer('kodebarang');
            $table->string('namabarang', 100);
            $table->integer('satuan');
            $table->string('namasatuan', 30)->nullable();
            $table->decimal('jumlah', 7, 2);
            $table->decimal('harga', 11, 2);
            $table->decimal('diskon', 11, 2)->nullable();
            $table->decimal('subtotal', 11, 2);
            $table->decimal('hppsubtotal', 11, 2)->nullable();
            $table->date('tglpenjualan');
            $table->decimal('saldoakhir', 7, 2)->default(0);
            $table->decimal('stokminimal', 7, 2)->nullable();
            $table->decimal('hargadiskon', 11, 2)->default(0);
            $table->decimal('jumlahkemasan', 7, 4)->default(0);
            $table->integer('kemasan')->nullable();
            $table->string('namakemasan', 30)->nullable();
            $table->decimal('isisatuan', 11, 2)->nullable();

            $table->primary(['nopenjualan', 'nourut']);
            $table->foreign('nopenjualan')->references('nopenjualan')->on('penjualan');
            $table->foreign('kodebarang')->references('kodebarang')->on('barang');
        });

        Schema::create('mutasibarang', function (Blueprint $table) {
            $table->integer('kodebarang')->primary();
            $table->decimal('saldoawal', 9, 2)->nullable();
            $table->decimal('beli', 9, 2)->nullable();
            $table->decimal('returbeli', 9, 2)->nullable();
            $table->decimal('keluar', 9, 2)->nullable();
            $table->decimal('returkeluar', 9, 2)->nullable();
            $table->decimal('jual', 9, 2)->nullable();
            $table->decimal('returjual', 9, 2)->nullable();
            $table->decimal('rakit', 9, 2)->nullable();
            $table->decimal('adjustmen', 9, 2)->nullable();
            $table->decimal('saldoakhir', 9, 2)->nullable();

            $table->foreign('kodebarang')->references('kodebarang')->on('barang');
        });

        Schema::create('fifostock', function (Blueprint $table) {
            $table->string('nobukti', 20);
            $table->integer('kodebarang');
            $table->integer('nourut');
            $table->timestamp('waktu');
            $table->decimal('harga', 11, 2);
            $table->decimal('masuk', 7, 2);
            $table->decimal('returmasuk', 7, 2);
            $table->decimal('keluar', 7, 2);
            $table->decimal('returkeluar', 7, 2);
            $table->decimal('saldo', 7, 2);
            $table->decimal('hargabonus', 11, 2)->default(0);

            $table->primary(['nobukti', 'kodebarang', 'nourut']);
            $table->foreign('kodebarang')->references('kodebarang')->on('barang');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fifostock');
        Schema::dropIfExists('mutasibarang');
        Schema::dropIfExists('penjualandetail');
        Schema::dropIfExists('penjualan');
        Schema::dropIfExists('penerimaandetail');
        Schema::dropIfExists('penerimaan');
    }
};
