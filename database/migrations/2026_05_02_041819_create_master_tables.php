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
        Schema::create('kelompok', function (Blueprint $table) {
            $table->integer('kelompok')->primary();
            $table->string('keterangan', 30);
        });

        Schema::create('kemasan', function (Blueprint $table) {
            $table->integer('kemasan')->primary();
            $table->string('keterangan', 30);
        });

        Schema::create('satuan', function (Blueprint $table) {
            $table->integer('satuan')->primary();
            $table->string('keterangan', 30);
        });

        Schema::create('supplier', function (Blueprint $table) {
            $table->integer('supplier')->primary();
            $table->string('keterangan', 30);
            // Add other nullable fields in case they exist in other parts of backup
            $table->string('namasupplier', 50)->nullable();
            $table->string('alamat', 100)->nullable();
            $table->string('kota', 30)->nullable();
            $table->string('telepon', 30)->nullable();
            $table->decimal('hutang', 11, 2)->default(0);
        });

        Schema::create('pelanggan', function (Blueprint $table) {
            $table->integer('kodepelanggan')->primary();
            $table->string('namapelanggan', 50);
            $table->string('kecamatan', 50)->nullable();
            $table->integer('limithari')->nullable();
            $table->decimal('limitrp', 10, 2)->nullable();
            $table->integer('hargajual')->nullable();
            // Fields below might be needed by our new app but missing in old schema
            $table->string('alamat', 100)->nullable();
            $table->string('kota', 30)->nullable();
            $table->string('telepon', 30)->nullable();
            $table->decimal('piutang', 11, 2)->default(0);
        });

        Schema::create('barang', function (Blueprint $table) {
            $table->integer('kodebarang')->primary();
            $table->string('namabarang', 50);
            $table->integer('kelompok');
            $table->integer('kemasan');
            $table->integer('satuan');
            $table->decimal('isisatuan', 6, 2)->nullable();
            $table->integer('supplier');
            $table->decimal('hargabeli', 9, 2);
            $table->string('lokasi', 10)->nullable();
            $table->decimal('stokminimal', 7, 2)->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('kontrol')->nullable();

            $table->foreign('kelompok')->references('kelompok')->on('kelompok');
            $table->foreign('kemasan')->references('kemasan')->on('kemasan');
            $table->foreign('satuan')->references('satuan')->on('satuan');
            $table->foreign('supplier')->references('supplier')->on('supplier');
        });
    }

    public function down()
    {
        Schema::dropIfExists('barang');
        Schema::dropIfExists('pelanggan');
        Schema::dropIfExists('supplier');
        Schema::dropIfExists('satuan');
        Schema::dropIfExists('kemasan');
        Schema::dropIfExists('kelompok');
    }
};
