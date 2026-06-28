<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salesman', function (Blueprint $table) {
            $table->integer('salesman')->primary();
            $table->string('keterangan', 30);
        });
    }

    public function down()
    {
        Schema::dropIfExists('salesman');
    }
};
