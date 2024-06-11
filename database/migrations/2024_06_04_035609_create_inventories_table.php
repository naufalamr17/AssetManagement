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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code'); // Kode Aset
            $table->string('old_asset_code')->nullable(); // Kode Aset Lama
            $table->string('pic_dept'); // PIC DEPT
            $table->string('location'); // LOCATION
            $table->string('asset_category'); // Kategori Aset
            $table->string('asset_position_dept'); // ASSET POSITION (DEPT)
            $table->string('asset_type'); // Jenis
            $table->text('description'); // Description
            $table->string('serial_number'); // Serial Number
            $table->date('acquisition_date'); // Tanggal Perolehan
            $table->date('disposal_date')->nullable(); // Tanggal Penghapusan
            $table->integer('useful_life')->nullable(); // Umur Ekonomis (tahun)
            $table->bigInteger('acquisition_value')->default(0); // Nilai Perolehan
            $table->enum('status', ['Good', 'Breakdown', 'Repair', 'Dispose'])->default('Good');
            $table->date('hand_over_date')->nullable();
            $table->string('user')->nullable();
            $table->string('dept')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
