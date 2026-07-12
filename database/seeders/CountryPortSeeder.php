<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryPortSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key check sementara agar proses delete aman dari error constraint
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('ports')->delete();
        DB::table('countries')->delete();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Tambah Data Negara Indonesia (Lengkap dengan semua kolom wajib)
        $countryId = DB::table('countries')->insertGetId([
            'name' => 'Indonesia',
            'iso2' => 'ID',
            'iso3' => 'IDN',
            'currency_code' => 'IDR',          // <-- TAMBAHKAN INI
            'region' => 'Southeast Asia',      // <-- TAMBAHKAN INI JUGA UNTUK JAGA-JAGA
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Tambah Pelabuhan Tanjung Priok
        DB::table('ports')->insert([
            'country_id' => $countryId,
            'port_name' => 'Tanjung Priok',
            'port_code' => 'IDTPK',
            'latitude' => -6.10,
            'longitude' => 106.87,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Tambah Pelabuhan Tanjung Perak
        DB::table('ports')->insert([
            'country_id' => $countryId,
            'port_name' => 'Tanjung Perak',
            'port_code' => 'IDTPR',
            'latitude' => -7.20,
            'longitude' => 112.73,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}