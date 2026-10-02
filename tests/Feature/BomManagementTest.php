<?php

namespace Tests\Feature;

use App\Models\Bom;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BomManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_boms_index_and_create_bom()
    {
        $user = User::first() ?? User::factory()->create();
        $this->actingAs($user);

        $prod = Product::first() ?? Product::create([
            'code' => 'TEST-OUTPUT-1',
            'name' => 'Produk Test Output',
            'type' => 'goods',
            'uom' => 'pcs',
            'cost_price' => 10000,
        ]);

        $comp = Product::where('id', '!=', $prod->id)->first() ?? Product::create([
            'code' => 'TEST-RAW-1',
            'name' => 'Bahan Baku Test',
            'type' => 'raw_material',
            'uom' => 'kg',
            'cost_price' => 5000,
        ]);

        $response = $this->get(route('manufacturing.boms.index'));
        $response->assertStatus(200);
        $response->assertSee('Bills of Materials');

        $responseCreate = $this->get(route('manufacturing.boms.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('New');

        // Store new BoM
        $postResponse = $this->post(route('manufacturing.boms.store'), [
            'product_id' => $prod->id,
            'code' => 'BOM-TEST-AUTO-01',
            'name' => 'BoM Test Otomatis',
            'type' => 'normal',
            'quantity' => 1.0,
            'uom' => 'pcs',
            'items' => [
                [
                    'product_id' => $comp->id,
                    'quantity' => 2.5,
                    'cones' => 4,
                    'uom' => 'kg',
                    'wastage_percent' => 5,
                ]
            ]
        ]);

        $postResponse->assertRedirect();
        
        $bom = Bom::where('code', 'BOM-TEST-AUTO-01')->first();
        $this->assertNotNull($bom);
        $this->assertEquals(1, $bom->items()->count());
        $this->assertEquals(2.5, (float)$bom->items->first()->quantity);

        // View BoM show
        $showResponse = $this->get(route('manufacturing.boms.show', $bom->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('BOM-TEST-AUTO-01');
    }
}
