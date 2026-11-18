<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criterion_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criterion_id')->constrained('criteria')->cascadeOnDelete();
            $table->string('condition_name');
            $table->text('condition_description')->nullable();
            $table->decimal('score', 5, 2);
            $table->json('rule_configuration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['criterion_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criterion_scales');
    }
};
