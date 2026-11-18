<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scan_criterion_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scan_id')->constrained('scans')->cascadeOnDelete();
            $table->foreignId('criterion_id')->nullable()->constrained('criteria')->nullOnDelete();
            $table->string('criterion_code_snapshot', 10);
            $table->string('criterion_name_snapshot');
            $table->decimal('criterion_weight_snapshot', 8, 4);
            $table->string('raw_value')->nullable();
            $table->decimal('score', 5, 2);
            $table->decimal('normalized_value', 10, 6);
            $table->decimal('weighted_contribution', 10, 6);
            $table->text('evidence')->nullable();
            $table->string('data_source')->nullable();
            $table->string('data_status', 40)->default('available');
            $table->timestamps();

            $table->index('scan_id');
            $table->index('criterion_code_snapshot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_criterion_results');
    }
};
