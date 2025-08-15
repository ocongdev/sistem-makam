<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('almarhums', function (Blueprint $table) {
    $table->id();
    $table->string('nama');
    $table->date('tanggal_lahir');
    $table->date('tanggal_wafat');
    $table->string('blok_makam');
    $table->string('nomor_makam');
    $table->string('foto')->nullable();
    $table->text('riwayat')->nullable();
    $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('almarhums');
    }
};
