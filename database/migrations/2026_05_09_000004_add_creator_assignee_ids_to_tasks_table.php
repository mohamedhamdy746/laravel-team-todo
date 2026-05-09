<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('creator_id')->nullable()->after('id');
            $table->unsignedBigInteger('assignee_id')->nullable()->after('creator_id');
            $table->index('creator_id');
            $table->index('assignee_id');
        });

        DB::table('tasks')->update([
            'creator_id' => DB::raw('user_id'),
            'assignee_id' => DB::raw('user_id'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['creator_id']);
            $table->dropIndex(['assignee_id']);
            $table->dropColumn(['creator_id', 'assignee_id']);
        });
    }
};
