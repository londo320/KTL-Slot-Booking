<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Depot;

class DepotSeeder extends Seeder
{
    public function run(): void
    {
        Depot::firstOrCreate(['name' => 'Wimblington'], ['location' => 'March']);
        Depot::firstOrCreate(['name' => 'Cromwell Road'], ['location' => 'Wisbech']);
        Depot::firstOrCreate(['name' => 'Salters Yard'], ['location' => 'Wisbech']);
        Depot::firstOrCreate(['name' => 'Lynn Road'], ['location' => 'Wisbech']);
    }
}
