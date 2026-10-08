<?php

use Illuminate\Database\Migrations\Migration;
use Webkul\Inventory\Enums\GroupPropagation;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Enums\RuleAuto;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Rule;
use Webkul\Manufacturing\Models\Warehouse;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (Warehouse::withTrashed()->get() as $warehouse) {
            if (! $warehouse->pbm_route_id || ! $warehouse->manu_type_id) {
                continue;
            }

            $productionLocation = Location::where('type', LocationType::PRODUCTION)
                ->where('company_id', $warehouse->company_id)
                ->first();

            if (! $productionLocation) {
                continue;
            }

            // Ensure manufacture route is active and product selectable
            Route::withTrashed()->where('id', $warehouse->pbm_route_id)->update([
                'deleted_at'         => null,
                'product_selectable' => true,
            ]);

            // Check if MANUFACTURE action rule already exists for this route
            $existingRule = Rule::withTrashed()
                ->where('route_id', $warehouse->pbm_route_id)
                ->where('action', RuleAction::MANUFACTURE)
                ->first();

            if ($existingRule) {
                $existingRule->update([
                    'deleted_at'              => null,
                    'source_location_id'      => $productionLocation->id,
                    'destination_location_id' => $warehouse->lot_stock_location_id,
                    'operation_type_id'       => $warehouse->manu_type_id,
                    'procure_method'          => ProcureMethod::MAKE_TO_ORDER,
                    'warehouse_id'            => $warehouse->id,
                ]);

                $warehouse->updateQuietly(['manufacture_pull_id' => $existingRule->id]);
            } else {
                $rule = Rule::create([
                    'sort'                     => 14,
                    'name'                     => $warehouse->code.': Manufacture',
                    'route_sort'               => 10,
                    'group_propagation_option' => GroupPropagation::PROPAGATE,
                    'action'                   => RuleAction::MANUFACTURE,
                    'procure_method'           => ProcureMethod::MAKE_TO_ORDER,
                    'auto'                     => RuleAuto::MANUAL,
                    'propagate_cancel'         => false,
                    'propagate_carrier'        => false,
                    'source_location_id'       => $productionLocation->id,
                    'destination_location_id'  => $warehouse->lot_stock_location_id,
                    'route_id'                 => $warehouse->pbm_route_id,
                    'operation_type_id'        => $warehouse->manu_type_id,
                    'creator_id'               => $warehouse->creator_id,
                    'company_id'               => $warehouse->company_id,
                    'warehouse_id'             => $warehouse->id,
                    'deleted_at'               => null,
                ]);

                $warehouse->updateQuietly(['manufacture_pull_id' => $rule->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Rule::where('action', RuleAction::MANUFACTURE)->delete();
    }
};
