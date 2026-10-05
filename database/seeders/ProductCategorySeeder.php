<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        $hasProductsCategories = Schema::hasTable('products_categories');
        $hasProductCategories = Schema::hasTable('product_categories');

        // Resolve creator ID for products_categories if users table exists
        $creatorId = null;
        if (Schema::hasTable('users')) {
            $creatorId = DB::table('users')->value('id');
        }

        // Cache for created categories to resolve parents quickly
        $categoryCache = [];
        $webkulCache = [];

        foreach ($categories as $rawCategory) {
            $rawCategory = trim($rawCategory);
            if (empty($rawCategory)) {
                continue;
            }

            // Check if it's hierarchical (contains /)
            if (str_contains($rawCategory, '/')) {
                $parts = array_map('trim', explode('/', $rawCategory));
                $parentId = null;
                $webkulParentId = null;
                $accumulatedPath = '';

                foreach ($parts as $index => $part) {
                    $accumulatedPath = $accumulatedPath === '' ? $part : "$accumulatedPath / $part";

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

                    // 1. Seed custom product_categories table
                    if ($hasProductCategories) {
                        if (!isset($categoryCache[$accumulatedPath])) {
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

                    // 2. Seed Webkul products_categories table (used by ERP: Inventory, Products, Sales, etc.)
                    if ($hasProductsCategories) {
                        if (!isset($webkulCache[$accumulatedPath])) {
                            $existing = DB::table('products_categories')
                                ->where('name', $part)
                                ->where('parent_id', $webkulParentId)
                                ->first();

                            if ($existing) {
                                $webkulCache[$accumulatedPath] = $existing->id;
                            } else {
                                $webkulParent = $webkulParentId ? DB::table('products_categories')->find($webkulParentId) : null;
                                $parentPath = $webkulParent ? ($webkulParent->parent_path . $webkulParent->id . '/') : '/';
                                $fullName = $webkulParent ? ($webkulParent->full_name . ' / ' . $part) : $part;

                                $webkulId = DB::table('products_categories')->insertGetId([
                                    'name' => $part,
                                    'full_name' => $fullName,
                                    'parent_path' => $parentPath,
                                    'parent_id' => $webkulParentId,
                                    'creator_id' => $creatorId,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                $webkulCache[$accumulatedPath] = $webkulId;
                            }
                        }
                        $webkulParentId = $webkulCache[$accumulatedPath];
                    }
                }
            } else {
                // Standalone / Root category
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

                // 1. Seed custom product_categories table
                if ($hasProductCategories && !isset($categoryCache[$rawCategory])) {
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

                // 2. Seed Webkul products_categories table (used by ERP: Inventory, Products, Sales, etc.)
                if ($hasProductsCategories && !isset($webkulCache[$rawCategory])) {
                    $existing = DB::table('products_categories')
                        ->where('name', $rawCategory)
                        ->whereNull('parent_id')
                        ->first();

                    if ($existing) {
                        $webkulCache[$rawCategory] = $existing->id;
                    } else {
                        $webkulId = DB::table('products_categories')->insertGetId([
                            'name' => $rawCategory,
                            'full_name' => $rawCategory,
                            'parent_path' => '/',
                            'parent_id' => null,
                            'creator_id' => $creatorId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $webkulCache[$rawCategory] = $webkulId;
                    }
                }
            }
        }
    }
}