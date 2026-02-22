<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataJobsTable extends Migration
{
    public function up()
    {
        Schema::create('data_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->string('format');
            $table->string('status');
            $table->string('source')->nullable();
            $table->json('mapping')->nullable();
            $table->json('validation')->nullable();
            $table->json('stats')->nullable();
            $table->text('error')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->index(['created_by']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('data_jobs');
    }
}
