<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $salesmen = DB::table('salesman')->get();

        foreach ($salesmen as $salesman) {
            DB::table('users')->insert([
                'salesman_id' => $salesman->salesman,
                'name' => $salesman->keterangan,
                'username' => strtolower(str_replace(' ', '', $salesman->keterangan)),
                'password' => Hash::make('password'),
                'role' => $salesman->salesman == 1 ? 'owner' : 'karyawan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
