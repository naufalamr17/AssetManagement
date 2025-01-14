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
        Schema::table('berita_acaras', function (Blueprint $table) {
            $table->string('nama')->nullable()->change();
            $table->string('nik')->nullable()->change();
            $table->string('dept')->nullable()->change();
            $table->string('jabatan')->nullable()->change();
            $table->string('alamat')->nullable()->change();
            $table->string('no_asset')->nullable()->change();
            $table->date('tanggal')->nullable()->change();
            $table->text('alasan')->nullable()->change();
            $table->text('kronologi')->nullable()->change();
            $table->string('nama_2')->nullable()->change();
            $table->string('nik_2')->nullable()->change();
            $table->string('dept_2')->nullable()->change();
            $table->string('jabatan_2')->nullable()->change();
            $table->string('tujuan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita_acaras', function (Blueprint $table) {
            $table->string('nama')->nullable(false)->change();
            $table->string('nik')->nullable(false)->change();
            $table->string('dept')->nullable(false)->change();
            $table->string('jabatan')->nullable(false)->change();
            $table->string('alamat')->nullable(false)->change();
            $table->string('no_asset')->nullable(false)->change();
            $table->date('tanggal')->nullable(false)->change();
            $table->text('alasan')->nullable(false)->change();
            $table->text('kronologi')->nullable(false)->change();
            $table->string('nama_2')->nullable(false)->change();
            $table->string('nik_2')->nullable(false)->change();
            $table->string('dept_2')->nullable(false)->change();
            $table->string('jabatan_2')->nullable(false)->change();
            $table->string('tujuan')->nullable(false)->change();
        });
    }
};
