<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weight_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('criterion_id')->nullable()->constrained('criteria')->nullOnDelete();
            $table->string('criterion_code', 10)->nullable();
            $table->decimal('old_weight', 8, 4)->nullable();
            $table->decimal('new_weight', 8, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weight_change_logs');
    }
};
