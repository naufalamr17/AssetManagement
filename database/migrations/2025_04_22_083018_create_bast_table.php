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
        Schema::create('bast', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained()->onDelete('cascade');
            $table->string('nama');
            $table->string('nik');
            $table->string('jabatan');
            $table->string('nama_2');
            $table->string('nik_2');
            $table->string('jabatan_2');
            $table->string('place');
            $table->date('tanggal');
            $table->string('barang');
            $table->string('kodeprod');
            $table->integer('qty');
            $table->string('satuan');
            $table->text('deskripsi');
            $table->text('alasan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bast');
    }
};
