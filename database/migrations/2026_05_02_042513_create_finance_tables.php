<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Accounts Payable (Hutang)
        Schema::create('ap', function (Blueprint $table) {
            $table->string('noap', 20)->primary();
            $table->date('tglap');
            $table->string('nopenerimaan', 20);
            $table->integer('supplier');
            $table->string('namasupplier', 30);
            $table->date('tgljatuhtempo');
            $table->decimal('total', 11, 2);
            $table->decimal('tunai', 11, 2);
            $table->decimal('kredit', 11, 2);
            $table->decimal('bayar', 11, 2)->default(0);
            $table->decimal('sisa', 11, 2)->default(0);
            $table->decimal('retur', 11, 2)->default(0);

            $table->foreign('supplier')->references('supplier')->on('supplier');
        });

        // Accounts Receivable (Piutang)
        Schema::create('ar', function (Blueprint $table) {
            $table->string('noar', 20)->primary();
            $table->date('tglar');
            $table->string('nopenjualan', 20);
            $table->integer('pelanggan');
            $table->string('namapelanggan', 30);
            $table->date('tgljatuhtempo');
            $table->decimal('total', 11, 2);
            $table->decimal('tunai', 11, 2);
            $table->decimal('kredit', 11, 2);
            $table->decimal('bayar', 11, 2)->default(0);
            $table->decimal('sisa', 11, 2)->default(0);

            $table->foreign('pelanggan')->references('kodepelanggan')->on('pelanggan');
        });

        // Cash Out (Payments of AP)
        Schema::create('kaskeluar', function (Blueprint $table) {
            $table->string('nokaskeluar', 20)->primary();
            $table->date('tanggal');
            $table->string('noref', 20);
            $table->string('keterangan', 200);
            $table->decimal('jumlah', 12, 2);
            $table->string('nama', 100)->nullable();
            $table->boolean('langsung')->default(false);
        });

        // Cash In (Payments of AR)
        Schema::create('kasmasuk', function (Blueprint $table) {
            $table->string('nokasmasuk', 20)->primary();
            $table->date('tanggal');
            $table->string('noref', 20);
            $table->string('keterangan', 200);
            $table->decimal('jumlah', 12, 2);
            $table->string('nama', 100)->nullable();
            $table->boolean('langsung')->default(false);
        });

        // Operational Expenses
        Schema::create('biaya', function (Blueprint $table) {
            $table->string('nobiaya', 20)->primary();
            $table->date('tglbiaya');
            $table->string('jenisbiaya', 200);
            $table->decimal('jumlahbiaya', 11, 2);
        });
    }

    public function down()
    {
        Schema::dropIfExists('biaya');
        Schema::dropIfExists('kasmasuk');
        Schema::dropIfExists('kaskeluar');
        Schema::dropIfExists('ar');
        Schema::dropIfExists('ap');
    }
};
