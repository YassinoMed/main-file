<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('integrations')->default(1)->after('production');
            $table->integer('sales')->default(1)->after('integrations');
            $table->integer('wms')->default(1)->after('sales');
            $table->integer('mrp')->default(1)->after('wms');
            $table->integer('quality')->default(1)->after('mrp');
            $table->integer('maintenance')->default(1)->after('quality');
            $table->integer('enterprise_accounting')->default(1)->after('maintenance');
            $table->integer('approvals')->default(1)->after('enterprise_accounting');
            $table->integer('hr_ops')->default(1)->after('approvals');
            $table->integer('saas')->default(1)->after('hr_ops');
        });
    }

    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'integrations',
                'sales',
                'wms',
                'mrp',
                'quality',
                'maintenance',
                'enterprise_accounting',
                'approvals',
                'hr_ops',
                'saas',
            ]);
        });
    }
};
