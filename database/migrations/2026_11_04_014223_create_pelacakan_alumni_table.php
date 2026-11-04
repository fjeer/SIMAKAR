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
        Schema::create('pelacakan_alumni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pengguna')->unique()->constrained('pengguna')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun_lulus');
            $table->string('status_saat_ini');
            $table->string('sektor_industri')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->string('jabatan')->nullable();
            $table->boolean('publik_di_direktori')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelacakan_alumni');
    }
};
