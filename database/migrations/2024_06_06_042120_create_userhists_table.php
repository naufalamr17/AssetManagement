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
        Schema::create('userhists', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('inv_id'); // Menggunakan unsignedBigInteger untuk merujuk ke ID pengguna
            $table->foreign('inv_id')->references('id')->on('inventories')->onDelete('cascade');
            $table->date('hand_over_date')->nullable();
            $table->string('user')->nullable();
            $table->string('dept')->nullable();
            $table->string('note')->nullable();
            $table->timestamps(); // Menambahkan kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userhists');
    }
};
