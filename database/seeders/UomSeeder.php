<?php

namespace Database\Seeders;

use App\Models\Uom;
use App\Models\UomCategory;
use Illuminate\Database\Seeder;

class UomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Unit' => [
                ['name' => 'Units', 'uom_type' => 'reference', 'ratio' => 1.0, 'rounding' => 0.01],
                ['name' => 'Pcs', 'uom_type' => 'reference', 'ratio' => 1.0, 'rounding' => 0.01],
                ['name' => 'Dozens', 'uom_type' => 'bigger', 'ratio' => 12.0, 'rounding' => 0.001],
                ['name' => 'Pairs', 'uom_type' => 'bigger', 'ratio' => 2.0, 'rounding' => 0.01],
                ['name' => 'Cone', 'uom_type' => 'reference', 'ratio' => 1.0, 'rounding' => 0.01],
                ['name' => 'Roll', 'uom_type' => 'reference', 'ratio' => 1.0, 'rounding' => 0.01],
                ['name' => 'Pack (3)', 'uom_type' => 'bigger', 'ratio' => 3.0, 'rounding' => 0.01],
                ['name' => 'Box (100)', 'uom_type' => 'bigger', 'ratio' => 100.0, 'rounding' => 0.01],
            ],
            'Weight' => [
                ['name' => 'kg', 'uom_type' => 'reference', 'ratio' => 1.0, 'rounding' => 0.001],
                ['name' => 'g', 'uom_type' => 'smaller', 'ratio' => 0.001, 'rounding' => 0.01],
                ['name' => 'Ton', 'uom_type' => 'bigger', 'ratio' => 1000.0, 'rounding' => 0.001],
                ['name' => 'lbs', 'uom_type' => 'smaller', 'ratio' => 0.453592, 'rounding' => 0.001],
            ],
            'Length / Distance' => [
                ['name' => 'm', 'uom_type' => 'reference', 'ratio' => 1.0, 'rounding' => 0.01],
                ['name' => 'cm', 'uom_type' => 'smaller', 'ratio' => 0.01, 'rounding' => 0.01],
                ['name' => 'mm', 'uom_type' => 'smaller', 'ratio' => 0.001, 'rounding' => 0.01],
                ['name' => 'Yard', 'uom_type' => 'smaller', 'ratio' => 0.9144, 'rounding' => 0.01],
                ['name' => 'Inch', 'uom_type' => 'smaller', 'ratio' => 0.0254, 'rounding' => 0.01],
            ],
            'Volume' => [
                ['name' => 'L', 'uom_type' => 'reference', 'ratio' => 1.0, 'rounding' => 0.01],
                ['name' => 'mL', 'uom_type' => 'smaller', 'ratio' => 0.001, 'rounding' => 0.01],
                ['name' => 'm³', 'uom_type' => 'bigger', 'ratio' => 1000.0, 'rounding' => 0.001],
            ],
            'Working Time' => [
                ['name' => 'Hours', 'uom_type' => 'reference', 'ratio' => 1.0, 'rounding' => 0.01],
                ['name' => 'Days', 'uom_type' => 'bigger', 'ratio' => 8.0, 'rounding' => 0.01],
                ['name' => 'Minutes', 'uom_type' => 'smaller', 'ratio' => 0.016667, 'rounding' => 0.01],
            ],
        ];

        foreach ($data as $categoryName => $uoms) {
            $cat = UomCategory::firstOrCreate(['name' => $categoryName]);
            foreach ($uoms as $uomData) {
                Uom::updateOrCreate(
                    [
                        'category_id' => $cat->id,
                        'name' => $uomData['name'],
                    ],
                    [
                        'uom_type' => $uomData['uom_type'],
                        'ratio' => $uomData['ratio'],
                        'rounding' => $uomData['rounding'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
