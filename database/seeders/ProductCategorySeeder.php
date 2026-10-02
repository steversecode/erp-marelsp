<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    /**
     * Seed product categories exported from Odoo.
     */
    public function run(): void
    {
        $categories = [
            'APD Mecs',
            'Aksesoris Baju',
            'Aksesoris KK',
            'Aksesoris Sewing',
            'Alat Kerja',
            'All',
            'All / Deliveries',
            'All / Expenses',
            'All / Saleable',
            'Artine',
            'Bahan Bakar',
            'Bahan Kimia',
            'Bahan Shoe Sock',
            'Baju / BIEMI',
            'Baju / MECS',
            'Baju Dokter Mecs',
            'Baju LC',
            'Baju Tanpa Gramasi',
            'Barang Afal',
            'Benang',
            'Benang / Bordir',
            'Benang / Kaoskaki',
            'Benang Baju',
            'Benang KK',
            'Bordir',
            'Braum',
            'Buy Sample Kaoskaki',
            'Dokumen',
            'Down payment',
            'GARMENT / GROSIR',
            'GARMENT/ARTINE',
            'GARMENT/BIEMI',
            'GARMENT/CLA BY CLA',
            'GARMENT/MAREL',
            'Jasa/kirim',
            'Job',
            'Jojo Mask',
            'KAOS KAKI / BIEMI',
            'KAOS KAKI / Grade B',
            'KAOS KAKI / Job Order',
            'KAOS KAKI / M SPORT',
            'KAOS KAKI / MECS',
            'KAOS KAKI / Marel Socks',
            'KAOS KAKI / Online Custom',
            'KAOS KAKI / Online Mitra',
            'KAOS KAKI / Sample Kaoskaki',
            'Kain',
            'Kain (bahan baku baju)',
            'Kain Baju',
            'Kaos kaki lupa PPS',
            'Kaoskaki Lama Marlin',
            'Lucky Cla',
            'Marlin',
            'Masker',
            'Masker LC',
            'Masker Mecs',
            'Mecs Apparel',
            'Mecs Lab',
            'Mesin',
            'Moslem Socks',
            'Ongkos',
            'Sample Baju',
            'Sample Benang',
            'Sample Bordir',
            'Sample Rajut',
            'School Socks',
            'Servis (Perawatan Inventaris)',
            'Shoe Sock tanpa PPS',
            'Shoe Socks',
            'Sparepart Bordir',
            'Sparepart KK',
            'Sparepart Sewing',
            'Sparepart Umum',
            'Umum',
            'Vendor Aksesoris',
            'Wristband',
            'job or',
        ];

        // Cache for created categories to resolve parents quickly
        $categoryCache = [];

        foreach ($categories as $rawCategory) {
            $rawCategory = trim($rawCategory);
            if (empty($rawCategory)) {
                continue;
            }

            // Check if it's hierarchical (contains /)
            if (str_contains($rawCategory, '/')) {
                $parts = array_map('trim', explode('/', $rawCategory));
                $parentId = null;
                $accumulatedPath = '';

                foreach ($parts as $index => $part) {
                    $accumulatedPath = $accumulatedPath === '' ? $part : "$accumulatedPath / $part";

                    if (!isset($categoryCache[$accumulatedPath])) {
                        $code = 'CAT-' . Str::upper(Str::slug($accumulatedPath, '-'));
                        if (strlen($code) > 50) {
                            $code = substr($code, 0, 50);
                        }

                        $costingMethod = 'average';
                        $valuationMethod = 'automated';

                        if (Str::contains(strtolower($part), ['jasa', 'ongkos', 'servis', 'service', 'deliveries', 'expenses'])) {
                            $costingMethod = 'standard';
                            $valuationMethod = 'manual';
                        } elseif (Str::contains(strtolower($part), ['kimia', 'chemical', 'dye'])) {
                            $costingMethod = 'fifo';
                        }

                        $category = ProductCategory::firstOrCreate(
                            ['name' => $part, 'parent_id' => $parentId],
                            [
                                'code' => $code,
                                'costing_method' => $costingMethod,
                                'valuation_method' => $valuationMethod,
                                'description' => "Odoo Category: {$accumulatedPath}",
                            ]
                        );

                        $categoryCache[$accumulatedPath] = $category->id;
                    }

                    $parentId = $categoryCache[$accumulatedPath];
                }
            } else {
                // Standalone / Root category
                if (!isset($categoryCache[$rawCategory])) {
                    $code = 'CAT-' . Str::upper(Str::slug($rawCategory, '-'));
                    if (strlen($code) > 50) {
                        $code = substr($code, 0, 50);
                    }

                    $costingMethod = 'average';
                    $valuationMethod = 'automated';

                    if (Str::contains(strtolower($rawCategory), ['jasa', 'ongkos', 'servis', 'service', 'expenses', 'dokumen'])) {
                        $costingMethod = 'standard';
                        $valuationMethod = 'manual';
                    } elseif (Str::contains(strtolower($rawCategory), ['kimia', 'chemical', 'dye'])) {
                        $costingMethod = 'fifo';
                    }

                    $category = ProductCategory::firstOrCreate(
                        ['name' => $rawCategory, 'parent_id' => null],
                        [
                            'code' => $code,
                            'costing_method' => $costingMethod,
                            'valuation_method' => $valuationMethod,
                            'description' => "Odoo Category: {$rawCategory}",
                        ]
                    );

                    $categoryCache[$rawCategory] = $category->id;
                }
            }
        }
    }
}