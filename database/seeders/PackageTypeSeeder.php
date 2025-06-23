<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subscription\PackageType;

class PackageTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PackageType::truncate();

        PackageType::create([
            'id' => \Str::uuid(),
            'name' => 'Free Plan',
            'description' => 'Fitur terbatas, maksimal 5 karyawan',
            'max_seats' => 5,
            'price_per_seat' => 0,
            'is_free' => true,
        ]);

        PackageType::create([
            'id' => \Str::uuid(),
            'name' => 'Standard',
            'description' => 'Cocok untuk startup kecil, hingga 100 karyawan',
            'max_seats' => 100,
            'price_per_seat' => 10000, // IDR 10.000 per seat/hari
            'is_free' => false,
        ]);

        PackageType::create([
            'id' => \Str::uuid(),
            'name' => 'Premium',
            'description' => 'Untuk perusahaan menengah, hingga 200 karyawan',
            'max_seats' => 200,
            'price_per_seat' => 15000,
            'is_free' => false,
        ]);
    }
}
