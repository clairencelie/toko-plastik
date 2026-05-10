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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('salesman_id')->nullable()->after('id');
            $table->string('username')->unique()->after('salesman_id');
            $table->string('role')->default('karyawan')->after('username');
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            
            $table->foreign('salesman_id')->references('salesman')->on('salesman')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['salesman_id']);
            $table->dropColumn(['salesman_id', 'username', 'role']);
            $table->string('name')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
        });
    }
};
