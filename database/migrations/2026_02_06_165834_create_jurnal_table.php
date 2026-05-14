<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal', function (Blueprint $table) {
            $table->id();

            // GANTI: dari constrained('guru') ke constrained('users')
            $table->unsignedBigInteger('guru_id');
            $table->foreign('guru_id')->references('id_user')->on('users')->cascadeOnDelete();

            $table->date('tanggal')->index();
            $table->string('jam_pelajaran');
            $table->string('mata_pelajaran')->index();
            $table->string('kelas')->index();
            $table->text('materi');
            $table->text('kendala')->nullable();

            $table->enum('status', ['pending', 'dinilai', 'revisi'])
                ->default('pending')
                ->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal');
    }
};