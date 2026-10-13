<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Inventory\Enums\GroupPropagation;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ReservationMethod;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Enums\RuleAuto;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class SetupYarnInventoryFlowCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:setup-yarn-flow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup automated Locations, Operation Types, Routes, and Rules for Yarn/Component flow (Return Sisa Produksi, Ke Sample, Ke Celup)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai konfigurasi alur inventaris benang (Yarn Flow)...');

        // 1. Dapatkan Warehouse, Company, dan Creator
        $warehouse = Warehouse::first();
        if (! $warehouse) {
            $this->error('Gagal: Warehouse tidak ditemukan di sistem.');

            return self::FAILURE;
        }

        $companyId = $warehouse->company_id ?? Company::first()?->id;
        $creatorId = User::first()?->id;

        $this->line("• Warehouse : <info>{$warehouse->name} ({$warehouse->code})</info>");
        $this->line("• Company ID: <info>{$companyId}</info>");

        // 2. Dapatkan Lokasi Dasar (Stock & Production)
        $stockLocation = $warehouse->lotStockLocation;
        if (! $stockLocation) {
            $stockLocation = Location::where('type', LocationType::INTERNAL)
                ->where('company_id', $companyId)
                ->first();
        }

        if (! $stockLocation) {
            $this->error('Gagal: Lokasi Stock utama tidak ditemukan.');

            return self::FAILURE;
        }

        $productionLocation = Location::where('type', LocationType::PRODUCTION)
            ->where('company_id', $companyId)
            ->first() ?? Location::where('type', LocationType::PRODUCTION)->first();

        if (! $productionLocation) {
            $this->error('Gagal: Lokasi Virtual Production tidak ditemukan.');

            return self::FAILURE;
        }

        $parentViewLocation = $warehouse->viewLocation ?? $stockLocation->parent ?? $stockLocation;

        $this->line("• Stock Loc : <info>{$stockLocation->full_name}</info>");
        $this->line("• Prod Loc  : <info>{$productionLocation->full_name}</info>");

        // ============================================================
        // 3. Buat / Pastikan Lokasi: WH/Sample & WH/Celup
        // ============================================================
        $this->newLine();
        $this->info('1. Memeriksa & Membuat Lokasi...');

        $sampleLocation = $this->ensureLocation(
            name: 'Sample',
            parentId: $parentViewLocation->id,
            type: LocationType::INTERNAL,
            companyId: $companyId,
            creatorId: $creatorId
        );
        $this->line("  ✓ Lokasi Sample : <info>{$sampleLocation->full_name}</info>");

        $celupLocation = $this->ensureLocation(
            name: 'Celup',
            parentId: $parentViewLocation->id,
            type: LocationType::INTERNAL,
            companyId: $companyId,
            creatorId: $creatorId
        );
        $this->line("  ✓ Lokasi Celup  : <info>{$celupLocation->full_name}</info>");

        // ============================================================
        // 4. Buat / Pastikan Operation Types
        // ============================================================
        $this->newLine();
        $this->info('2. Memeriksa & Membuat Operation Types...');

        // 4a. Return Sisa Produksi (Production -> Stock)
        $returnMoOpType = $this->ensureOperationType(
            name: 'Return Sisa Produksi',
            type: OperationTypeEnum::INTERNAL,
            sequenceCode: 'RET-MO',
            warehouseId: $warehouse->id,
            sourceLocationId: $productionLocation->id,
            destinationLocationId: $stockLocation->id,
            companyId: $companyId,
            creatorId: $creatorId
        );
        $this->line("  ✓ Op Type: <info>{$returnMoOpType->name}</info> (Prefix: {$warehouse->code}/{$returnMoOpType->sequence_code}/)");

        // 4b. Pengeluaran Sample (Stock -> Sample)
        $sampleOpType = $this->ensureOperationType(
            name: 'Pengeluaran Sample',
            type: OperationTypeEnum::INTERNAL,
            sequenceCode: 'SMPL',
            warehouseId: $warehouse->id,
            sourceLocationId: $stockLocation->id,
            destinationLocationId: $sampleLocation->id,
            companyId: $companyId,
            creatorId: $creatorId
        );
        $this->line("  ✓ Op Type: <info>{$sampleOpType->name}</info> (Prefix: {$warehouse->code}/{$sampleOpType->sequence_code}/)");

        // 4c. Pengiriman ke Celup (Stock -> Celup)
        $celupOpType = $this->ensureOperationType(
            name: 'Pengiriman ke Celup',
            type: OperationTypeEnum::INTERNAL,
            sequenceCode: 'DYE-OUT',
            warehouseId: $warehouse->id,
            sourceLocationId: $stockLocation->id,
            destinationLocationId: $celupLocation->id,
            companyId: $companyId,
            creatorId: $creatorId
        );
        $this->line("  ✓ Op Type: <info>{$celupOpType->name}</info> (Prefix: {$warehouse->code}/{$celupOpType->sequence_code}/)");

        // ============================================================
        // 5. Buat / Pastikan Routes & Rules
        // ============================================================
        $this->newLine();
        $this->info('3. Memeriksa & Membuat Routes & Rules...');

        // 5a. Route: Return Sisa Produksi
        $routeReturn = $this->ensureRoute(
            name: 'Return Sisa Produksi',
            companyId: $companyId,
            creatorId: $creatorId
        );
        $this->ensureRule(
            name: 'Production → WH/Stock (Return Sisa MO)',
            routeId: $routeReturn->id,
            action: RuleAction::PUSH,
            auto: RuleAuto::MANUAL,
            procureMethod: ProcureMethod::MAKE_TO_STOCK,
            operationTypeId: $returnMoOpType->id,
            sourceLocationId: $productionLocation->id,
            destinationLocationId: $stockLocation->id,
            warehouseId: $warehouse->id,
            companyId: $companyId,
            creatorId: $creatorId
        );
        $this->line("  ✓ Route & Rule: <info>{$routeReturn->name}</info>");

        // 5b. Route: Pengeluaran Benang ke Sample
        $routeSample = $this->ensureRoute(
            name: 'Pengeluaran Benang ke Sample',
            companyId: $companyId,
            creatorId: $creatorId
        );
        $this->ensureRule(
            name: 'WH/Stock → Sample (Permintaan Sample)',
            routeId: $routeSample->id,
            action: RuleAction::PULL,
            auto: RuleAuto::MANUAL,
            procureMethod: ProcureMethod::MAKE_TO_STOCK,
            operationTypeId: $sampleOpType->id,
            sourceLocationId: $stockLocation->id,
            destinationLocationId: $sampleLocation->id,
            warehouseId: $warehouse->id,
            companyId: $companyId,
            creatorId: $creatorId
        );
        $this->line("  ✓ Route & Rule: <info>{$routeSample->name}</info>");

        // 5c. Route: Kirim Benang ke Celup
        $routeCelup = $this->ensureRoute(
            name: 'Kirim Benang ke Celup',
            companyId: $companyId,
            creatorId: $creatorId
        );
        $this->ensureRule(
            name: 'WH/Stock → Celup (Kirim Mentah)',
            routeId: $routeCelup->id,
            action: RuleAction::PULL,
            auto: RuleAuto::MANUAL,
            procureMethod: ProcureMethod::MAKE_TO_STOCK,
            operationTypeId: $celupOpType->id,
            sourceLocationId: $stockLocation->id,
            destinationLocationId: $celupLocation->id,
            warehouseId: $warehouse->id,
            companyId: $companyId,
            creatorId: $creatorId
        );
        $this->line("  ✓ Route & Rule: <info>{$routeCelup->name}</info>");

        $this->newLine();
        $this->info('====================================================');
        $this->info('✓ SEMUA KONFIGURASI YARN FLOW BERHASIL DITERAPKAN!');
        $this->info('====================================================');

        return self::SUCCESS;
    }

    protected function ensureLocation(string $name, int $parentId, LocationType $type, int $companyId, ?int $creatorId): Location
    {
        $location = Location::withTrashed()
            ->where('company_id', $companyId)
            ->where('name', $name)
            ->where('parent_id', $parentId)
            ->first();

        if ($location) {
            if ($location->trashed()) {
                $location->restore();
            }

            return $location;
        }

        return Location::create([
            'name'       => $name,
            'parent_id'  => $parentId,
            'type'       => $type,
            'company_id' => $companyId,
            'creator_id' => $creatorId,
        ]);
    }

    protected function ensureOperationType(
        string $name,
        OperationTypeEnum $type,
        string $sequenceCode,
        int $warehouseId,
        int $sourceLocationId,
        int $destinationLocationId,
        int $companyId,
        ?int $creatorId
    ): OperationType {
        $opType = OperationType::withTrashed()
            ->where('company_id', $companyId)
            ->where('warehouse_id', $warehouseId)
            ->where('name', $name)
            ->first();

        if ($opType) {
            if ($opType->trashed()) {
                $opType->restore();
            }

            $opType->update([
                'type'                    => $type,
                'sequence_code'           => $sequenceCode,
                'source_location_id'      => $sourceLocationId,
                'destination_location_id' => $destinationLocationId,
            ]);

            $opType->ensureSequence();

            return $opType;
        }

        $opType = OperationType::create([
            'name'                    => $name,
            'type'                    => $type,
            'sequence_code'           => $sequenceCode,
            'warehouse_id'            => $warehouseId,
            'source_location_id'      => $sourceLocationId,
            'destination_location_id' => $destinationLocationId,
            'company_id'              => $companyId,
            'creator_id'              => $creatorId,
            'reservation_method'      => ReservationMethod::AT_CONFIRM,
        ]);

        $opType->ensureSequence();

        return $opType;
    }

    protected function ensureRoute(string $name, int $companyId, ?int $creatorId): Route
    {
        $route = Route::withTrashed()
            ->where('company_id', $companyId)
            ->where('name', $name)
            ->first();

        if ($route) {
            if ($route->trashed()) {
                $route->restore();
            }

            $route->update([
                'product_selectable'          => true,
                'product_category_selectable' => true,
            ]);

            return $route;
        }

        return Route::create([
            'name'                        => $name,
            'product_selectable'          => true,
            'product_category_selectable' => true,
            'warehouse_selectable'        => false,
            'packaging_selectable'        => false,
            'company_id'                  => $companyId,
            'creator_id'                  => $creatorId,
        ]);
    }

    protected function ensureRule(
        string $name,
        int $routeId,
        RuleAction $action,
        RuleAuto $auto,
        ProcureMethod $procureMethod,
        int $operationTypeId,
        int $sourceLocationId,
        int $destinationLocationId,
        int $warehouseId,
        int $companyId,
        ?int $creatorId
    ): Rule {
        $rule = Rule::withTrashed()
            ->where('company_id', $companyId)
            ->where('route_id', $routeId)
            ->where('name', $name)
            ->first();

        $attributes = [
            'name'                     => $name,
            'route_id'                 => $routeId,
            'action'                   => $action,
            'auto'                     => $auto,
            'procure_method'           => $procureMethod,
            'operation_type_id'        => $operationTypeId,
            'source_location_id'       => $sourceLocationId,
            'destination_location_id'  => $destinationLocationId,
            'warehouse_id'             => $warehouseId,
            'company_id'               => $companyId,
            'creator_id'               => $creatorId,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
        ];

        if ($rule) {
            if ($rule->trashed()) {
                $rule->restore();
            }

            $rule->update($attributes);

            return $rule;
        }

        return Rule::create($attributes);
    }
}
