<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['Administrator', 'Super Admin', 'Creator', 'Modified', 'Viewers', 'Auditor'])
                ->default('Administrator')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['Administrator', 'Super Admin', 'Creator', 'Modified', 'Viewers'])
                ->default('Administrator')
                ->change();
        });
    }
};
