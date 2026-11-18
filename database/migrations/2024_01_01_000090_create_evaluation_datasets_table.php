<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_datasets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('source')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('total_rows')->default(0);
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('evaluation_dataset_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained('evaluation_datasets')->cascadeOnDelete();
            $table->text('url');
            $table->string('ground_truth_label', 20);
            $table->string('source_reference')->nullable();
            $table->timestamps();

            $table->index('dataset_id');
            $table->index('ground_truth_label');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_dataset_rows');
        Schema::dropIfExists('evaluation_datasets');
    }
};
