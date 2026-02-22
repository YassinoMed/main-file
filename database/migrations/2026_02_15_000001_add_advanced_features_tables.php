<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('theme_mode')->default('light')->after('mode');
            $table->string('keyboard_shortcuts')->nullable()->after('theme_mode');
            $table->boolean('two_factor_enabled')->default(false)->after('keyboard_shortcuts');
            $table->string('pwa_notifications')->default('enabled')->after('two_factor_enabled');
            $table->timestamp('last_activity')->nullable()->after('pwa_notifications');
        });

        Schema::create('user_keyboard_shortcuts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('action');
            $table->string('shortcut');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('trigger_model');
            $table->json('trigger_conditions');
            $table->json('actions');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('workflow_executions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflow_id');
            $table->unsignedBigInteger('triggered_by');
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_type')->nullable();
            $table->text('execution_data')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->foreign('workflow_id')->references('id')->on('workflows')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('workflow_executions');
        Schema::dropIfExists('workflows');
        Schema::dropIfExists('user_keyboard_shortcuts');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['theme_mode', 'keyboard_shortcuts', 'two_factor_enabled', 'pwa_notifications', 'last_activity']);
        });
    }
};
