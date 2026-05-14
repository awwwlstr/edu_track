<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jurnal_id')
                ->constrained('jurnal')
                ->cascadeOnDelete();

            // GANTI: sesuaikan primary key id_user
            $table->unsignedBigInteger('kepsek_id');
            $table->foreign('kepsek_id')->references('id_user')->on('users')->cascadeOnDelete();

            $table->integer('nilai');
            $table->text('catatan')->nullable();

            $table->timestamps();

            // Satu jurnal hanya bisa dievaluasi satu kali
            $table->unique('jurnal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi');
    }
};