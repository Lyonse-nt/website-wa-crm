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
        Schema::create('percakapans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontak_id')->constrained('kontaks')->cascadeOnDelete();
            $table->text('pesan_terakhir')->nullable();
            $table->timestamp('waktu_pesan_terakhir')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('percakapans');
    }
};
