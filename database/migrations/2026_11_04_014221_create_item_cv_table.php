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
        Schema::create('item_cv', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_portofolio_cv')->constrained('portofolio_cv')->cascadeOnDelete();
            $table->string('tipe');
            $table->string('judul');
            $table->string('institusi');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('sumber_api');
            $table->boolean('disembunyikan')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_cv');
    }
};
