<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('license_number');
            $table->text('bio')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('specialist_specialty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('specialty_id')->constrained()->cascadeOnDelete();
            $table->unique(['specialist_id', 'specialty_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialist_specialty');
        Schema::dropIfExists('specialists');
    }
};
