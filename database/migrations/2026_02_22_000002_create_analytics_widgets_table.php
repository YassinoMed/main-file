<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalyticsWidgetsTable extends Migration
{
    public function up()
    {
        Schema::create('analytics_widgets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dashboard_id');
            $table->string('type');
            $table->json('config')->nullable();
            $table->json('position')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->index(['dashboard_id']);
            $table->index(['created_by']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('analytics_widgets');
    }
}
