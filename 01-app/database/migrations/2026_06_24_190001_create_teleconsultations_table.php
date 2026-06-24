<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teleconsultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->string('room_name');
            $table->string('room_url');
            $table->string('status')->default('waiting');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->text('consultation_notes')->nullable();
            $table->timestamps();

            $table->unique('appointment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teleconsultations');
    }
};
