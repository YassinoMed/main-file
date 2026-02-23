<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderToFormFieldsTable extends Migration
{
    public function up()
    {
        Schema::table('form_fields', function (Blueprint $table) {
            if (!Schema::hasColumn('form_fields', 'order')) {
                $table->integer('order')->default(0);
            }
        });
    }

    public function down()
    {
        Schema::table('form_fields', function (Blueprint $table) {
            if (Schema::hasColumn('form_fields', 'order')) {
                $table->dropColumn('order');
            }
        });
    }
}
