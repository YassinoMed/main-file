<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        $updates = [];
        foreach (['crm', 'hrm', 'account', 'project', 'pos', 'production', 'chatgpt'] as $column) {
            if (Schema::hasColumn('plans', $column)) {
                $updates[$column] = 1;
            }
        }

        if ($updates !== []) {
            DB::table('plans')->update($updates);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        $updates = [];
        foreach (['crm', 'hrm', 'account', 'project', 'pos', 'production', 'chatgpt'] as $column) {
            if (Schema::hasColumn('plans', $column)) {
                $updates[$column] = 0;
            }
        }

        if ($updates !== []) {
            DB::table('plans')->update($updates);
        }
    }
};

