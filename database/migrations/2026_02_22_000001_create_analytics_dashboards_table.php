<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalyticsDashboardsTable extends Migration
{
    public function up()
    {
        Schema::create('analytics_dashboards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('filters')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->index(['created_by']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('analytics_dashboards');
    }
}
