<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['label' => 'pending'],
            ['label' => 'paid'],
            ['label' => 'processing'],
            ['label' => 'shipping'],
            ['label' => 'completed'],
            ['label' => 'canceled'],
            ['label' => 'failed'],
            ['label' => 'expired'],
            ['label' => 'refunded'],
        ];

        DB::table('statuses')->insert($statuses);
    }
}
