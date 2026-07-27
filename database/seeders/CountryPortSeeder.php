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
        
        DB::table('ports')->truncate();
        DB::table('countries')->truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = \Faker\Factory::create();
        
        // Ensure some specific countries exist
        $specifics = [
            ['name' => 'Indonesia', 'iso2' => 'ID', 'iso3' => 'IDN', 'currency_code' => 'IDR', 'region' => 'Southeast Asia', 'port_name' => 'Tanjung Priok', 'lat' => -6.10, 'lng' => 106.87],
            ['name' => 'Singapore', 'iso2' => 'SG', 'iso3' => 'SGP', 'currency_code' => 'SGD', 'region' => 'Southeast Asia', 'port_name' => 'Port of Singapore', 'lat' => 1.26, 'lng' => 103.82],
            ['name' => 'China', 'iso2' => 'CN', 'iso3' => 'CHN', 'currency_code' => 'CNY', 'region' => 'East Asia', 'port_name' => 'Port of Shanghai', 'lat' => 31.23, 'lng' => 121.47],
            ['name' => 'United States', 'iso2' => 'US', 'iso3' => 'USA', 'currency_code' => 'USD', 'region' => 'North America', 'port_name' => 'Port of Los Angeles', 'lat' => 33.72, 'lng' => -118.27],
            ['name' => 'Germany', 'iso2' => 'DE', 'iso3' => 'DEU', 'currency_code' => 'EUR', 'region' => 'Europe', 'port_name' => 'Port of Hamburg', 'lat' => 53.54, 'lng' => 9.99],
        ];

        $countriesData = [];
        $portsData = [];
        $usedIso2 = [];

        foreach ($specifics as $spec) {
            $countryId = DB::table('countries')->insertGetId([
                'name' => $spec['name'],
                'iso2' => $spec['iso2'],
                'iso3' => $spec['iso3'],
                'currency_code' => $spec['currency_code'],
                'region' => $spec['region'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::table('ports')->insert([
                'country_id' => $countryId,
                'port_name' => $spec['port_name'],
                'port_code' => $spec['iso2'] . 'XXX',
                'latitude' => $spec['lat'],
                'longitude' => $spec['lng'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $usedIso2[] = $spec['iso2'];
        }

        // Generate the rest to make 250
        $count = 5;
        while ($count < 250) {
            $iso2 = strtoupper($faker->unique()->lexify('??'));
            if (in_array($iso2, $usedIso2)) continue;
            
            $usedIso2[] = $iso2;
            
            $countryId = DB::table('countries')->insertGetId([
                'name' => $faker->country . ' ' . $iso2, // append iso2 to ensure name uniqueness just in case
                'iso2' => $iso2,
                'iso3' => strtoupper($faker->unique()->lexify('???')),
                'currency_code' => $faker->currencyCode,
                'region' => $faker->word,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Generate 1-2 ports per country
            $numPorts = rand(1, 2);
            for ($i = 0; $i < $numPorts; $i++) {
                DB::table('ports')->insert([
                    'country_id' => $countryId,
                    'port_name' => 'Port of ' . $faker->city,
                    'port_code' => $iso2 . strtoupper($faker->lexify('???')),
                    'latitude' => $faker->latitude(-60, 70), // Keep it within somewhat realistic bounds for map display
                    'longitude' => $faker->longitude(-180, 180),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $count++;
        }
    }
}