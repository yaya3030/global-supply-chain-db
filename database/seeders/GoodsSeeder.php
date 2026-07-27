<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('goods_history')->truncate();
        DB::table('goods')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = \Faker\Factory::create();
        $ports = DB::table('ports')->pluck('id')->toArray();

        if (empty($ports)) {
            return; // No ports to seed goods for
        }

        $goodsNames = ['Electronics', 'Textiles', 'Automobile Parts', 'Medical Supplies', 'Food & Beverages', 'Chemicals', 'Machinery', 'Furniture', 'Toys', 'Oil & Gas'];
        $statuses = ['arrived', 'delayed', 'departed'];

        for ($i = 0; $i < 500; $i++) {
            $status = $faker->randomElement(['in_transit', 'arrived', 'delayed']);
            $goodId = DB::table('goods')->insertGetId([
                'name' => $faker->randomElement($goodsNames) . ' - ' . $faker->word,
                'tracking_number' => strtoupper($faker->bothify('TRK-####-????')),
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create a route history of 2 to 5 ports
            $numPorts = rand(2, 5);
            $visitedPorts = (array) array_rand(array_flip($ports), $numPorts);
            
            $time = now()->subDays(rand(10, 30));
            
            foreach ($visitedPorts as $index => $portId) {
                $historyStatus = 'departed';
                $departureTime = (clone $time)->addHours(rand(12, 48));

                // If it's the last port in history, it matches the good's current status
                if ($index == count($visitedPorts) - 1) {
                    if ($status == 'arrived' || $status == 'delayed') {
                        $historyStatus = $status;
                        $departureTime = null; // still there
                    }
                }

                DB::table('goods_history')->insert([
                    'good_id' => $goodId,
                    'port_id' => $portId,
                    'status' => $historyStatus,
                    'arrival_time' => $time,
                    'departure_time' => $departureTime,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($departureTime) {
                    $time = (clone $departureTime)->addDays(rand(1, 5));
                }
            }
        }
    }
}
