<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->string('company', 3)->default('MLP')->after('id')->index();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('company', 3)->default('MLP')->after('email')->index();
        });

        DB::table('inventories')->whereNull('company')->update(['company' => 'MLP']);
        DB::table('users')->whereNull('company')->update(['company' => 'MLP']);
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex(['company']);
            $table->dropColumn('company');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['company']);
            $table->dropColumn('company');
        });
    }
};
