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
        Schema::create('pesans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('percakapan_id')->constrained('percakapans')->cascadeOnDelete();
            $table->enum('arah_pesan', ['masuk', 'keluar']);
            $table->enum('jenis_pesan', ['text', 'image', 'document', 'template'])->default('text');
            $table->text('isi_pesan');
            $table->string('whatsapp_message_id')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('pending');
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesans');
    }
};
