<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a Villa
        $villa = \App\Models\Property::create([
            'name' => 'Sunset Villa',
            'type' => 'Villa',
            'units_count' => 1,
            'address' => '123 Palm Grove, Jumeirah',
            'location' => 'Dubai',
            'electricity_number' => [['number' => '123456789']],
            'water_number' => [['number' => '987654321']],
        ]);

        $this->createReadings($villa);

        // Create a Building with Units
        $building = \App\Models\Property::create([
            'name' => 'Skyline Heights',
            'type' => 'Building',
            'units_count' => 10,
            'address' => '456 Marina Blvd, Marina',
            'location' => 'Dubai',
            'electricity_number' => [['number' => 'E-1001'], ['number' => 'E-1002']],
            'water_number' => [['number' => 'W-2001']],
        ]);

        // Create units for the building
        for ($i = 1; $i <= 10; $i++) {
            $building->units()->create([
                'name' => 'A-' . (100 + $i),
                'type' => $i % 2 == 0 ? '2BHK' : '1BHK',
                'is_occupied' => rand(0, 1),
                'number_of_conditioning' => rand(1, 3),
                'number_of_people' => rand(1, 3),
                'number_of_rooms' => rand(1, 3),
                'description' => 'Standard unit',
                'electricity_number' => 'E-' . (100 + $i),
                'water_number' => 'W-' . (200 + $i),
            ]);
        }

        $this->createReadings($building);

        // Create a Compound
        $compound = \App\Models\Property::create([
            'name' => 'Green Oasis Compound',
            'type' => 'Compound',
            'units_count' => 5,
            'address' => '789 Desert Road, Arabian Ranches',
            'location' => 'Dubai',
            'electricity_number' => [['number' => 'C-5555']],
            'water_number' => [['number' => 'C-6666']],
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $compound->units()->create([
                'name' => 'Villa ' . $i,
                'type' => '3BHK Villa',
                'is_occupied' => true,
                'number_of_conditioning' => rand(1, 3),
                'number_of_people' => rand(1, 3),
                'number_of_rooms' => rand(1, 3),
                'description' => 'Standard unit',
                'electricity_number' => 'C-' . (5555 + $i),
                'water_number' => 'C-' . (6666 + $i),
            ]);
        }

        $this->createReadings($compound);
    }

    private function createReadings(\App\Models\Property $property): void
    {
        // Create readings for the last 6 months
        for ($i = 0; $i < 6; $i++) {
            $date = now()->subMonths(5 - $i)->startOfMonth();

            // Electricity
            $property->readings()->create([
                'type' => 'electricity',
                'value' => rand(500, 2000),
                'reading_date' => $date,
            ]);

            // Water
            $property->readings()->create([
                'type' => 'water',
                'value' => rand(100, 500),
                'reading_date' => $date,
            ]);
        }
    }
}
