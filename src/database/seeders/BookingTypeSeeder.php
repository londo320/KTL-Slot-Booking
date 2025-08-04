<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\BookingType;
class BookingTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Palletised', 'slots_required' => 1],
            ['name' => 'Handball',   'slots_required' => 2],
            ['name' => 'Ton Bags',   'slots_required' => 1],
        ];
        foreach ($types as $t) {
            BookingType::firstOrCreate(['name' => $t['name']], $t);
        }
    }
}