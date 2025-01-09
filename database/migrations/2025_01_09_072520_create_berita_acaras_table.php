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
        Schema::create('berita_acaras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained()->onDelete('cascade');
            $table->string('nama');
            $table->string('nik');
            $table->string('dept');
            $table->string('jabatan');
            $table->string('alamat');
            $table->string('no_asset');
            $table->date('tanggal');
            $table->text('alasan');
            $table->text('kronologi');
            $table->string('nama_2');
            $table->string('nik_2');
            $table->string('jabatan_2');
            $table->string('tujuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_acaras');
    }
};
