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
        Schema::create('accesses', function (Blueprint $table) {
            $table->id(); // Menetapkan kolom 'id' sebagai kunci utama auto-increment
            $table->unsignedBigInteger('user_id'); // Menggunakan unsignedBigInteger untuk merujuk ke ID pengguna
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('access', ['Asset Management', 'IT Asset Management', 'ATK Management']); // ENUM access
            $table->timestamps(); // Menambahkan kolom created_at dan updated_at
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accesses');
    }
};
