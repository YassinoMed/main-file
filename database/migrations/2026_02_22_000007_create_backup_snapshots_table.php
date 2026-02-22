<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBackupSnapshotsTable extends Migration
{
    public function up()
    {
        Schema::create('backup_snapshots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('provider');
            $table->string('location');
            $table->string('status');
            $table->json('metadata')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->index(['created_by']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('backup_snapshots');
    }
}
