<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('production_work_centers')) {
            Schema::create('production_work_centers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type')->default('machine');
                $table->decimal('cost_per_hour', 16, 2)->default(0);
                $table->integer('created_by')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('production_boms')) {
            Schema::create('production_boms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->string('code')->nullable();
                $table->string('name');
                $table->unsignedBigInteger('active_bom_version_id')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'product_id'], 'prod_boms_creator_product_idx');
            });
        }

        if (!Schema::hasTable('production_bom_versions')) {
            Schema::create('production_bom_versions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('production_bom_id');
                $table->string('version')->default('1');
                $table->date('effective_from')->nullable();
                $table->date('effective_to')->nullable();
                $table->boolean('is_active')->default(false);
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'production_bom_id'], 'prod_bom_versions_creator_bom_idx');
            });
        }

        if (!Schema::hasTable('production_bom_lines')) {
            Schema::create('production_bom_lines', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('production_bom_version_id');
                $table->unsignedBigInteger('component_product_id');
                $table->decimal('quantity', 16, 4)->default(0);
                $table->decimal('scrap_percent', 5, 2)->default(0);
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'production_bom_version_id'], 'prod_bom_lines_creator_version_idx');
            });
        }

        if (!Schema::hasTable('production_orders')) {
            Schema::create('production_orders', function (Blueprint $table) {
                $table->id();
                $table->integer('order_number')->default(0);
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('production_bom_version_id')->nullable();
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->unsignedBigInteger('work_center_id')->nullable();
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->decimal('quantity_planned', 16, 4)->default(0);
                $table->decimal('quantity_produced', 16, 4)->default(0);
                $table->date('planned_start_date')->nullable();
                $table->date('planned_end_date')->nullable();
                $table->string('priority')->default('normal');
                $table->string('status')->default('draft');
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->unique(['created_by', 'order_number'], 'prod_orders_creator_number_uniq');
                $table->index(['created_by', 'status'], 'prod_orders_creator_status_idx');
                $table->index(['created_by', 'planned_start_date'], 'prod_orders_creator_start_idx');
            });
        }

        if (!Schema::hasTable('production_order_operations')) {
            Schema::create('production_order_operations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('production_order_id');
                $table->string('name');
                $table->integer('sequence')->default(1);
                $table->unsignedBigInteger('work_center_id')->nullable();
                $table->integer('planned_minutes')->default(0);
                $table->integer('actual_minutes')->default(0);
                $table->string('status')->default('pending');
                $table->timestamp('started_at')->nullable();
                $table->timestamp('finished_at')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'production_order_id'], 'prod_ops_creator_order_idx');
            });
        }

        if (!Schema::hasTable('production_material_moves')) {
            Schema::create('production_material_moves', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('production_order_id');
                $table->unsignedBigInteger('component_product_id');
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->decimal('required_qty', 16, 4)->default(0);
                $table->decimal('reserved_qty', 16, 4)->default(0);
                $table->decimal('consumed_qty', 16, 4)->default(0);
                $table->timestamp('reserved_at')->nullable();
                $table->timestamp('consumed_at')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->unique(['production_order_id', 'component_product_id'], 'prod_moves_order_component_uniq');
                $table->index(['created_by', 'warehouse_id', 'component_product_id'], 'prod_moves_creator_wh_comp_idx');
            });
        }

        if (!Schema::hasTable('production_time_logs')) {
            Schema::create('production_time_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('production_order_operation_id');
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->unsignedBigInteger('work_center_id')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->integer('minutes')->default(0);
                $table->decimal('hourly_rate', 16, 2)->default(0);
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'production_order_operation_id'], 'prod_timelogs_creator_op_idx');
            });
        }

        if (!Schema::hasTable('production_quality_checks')) {
            Schema::create('production_quality_checks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('production_order_id');
                $table->unsignedBigInteger('production_order_operation_id')->nullable();
                $table->string('check_point');
                $table->string('result')->default('pass');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->timestamp('checked_at')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'production_order_id'], 'prod_qc_creator_order_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('production_quality_checks');
        Schema::dropIfExists('production_time_logs');
        Schema::dropIfExists('production_material_moves');
        Schema::dropIfExists('production_order_operations');
        Schema::dropIfExists('production_orders');
        Schema::dropIfExists('production_bom_lines');
        Schema::dropIfExists('production_bom_versions');
        Schema::dropIfExists('production_boms');
        Schema::dropIfExists('production_work_centers');
    }
};
