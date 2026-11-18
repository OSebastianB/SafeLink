<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('original_url');
            $table->text('normalized_url');
            $table->string('domain')->nullable();
            $table->string('protocol', 20)->nullable();
            $table->decimal('security_score', 10, 6)->nullable();
            $table->decimal('risk_index', 8, 4)->nullable();
            $table->string('risk_category', 30)->nullable();
            $table->decimal('data_coverage_percentage', 5, 2)->nullable();
            $table->string('threat_match_status', 40)->nullable();
            $table->text('warning_message')->nullable();
            $table->text('explanation')->nullable();
            $table->json('url_features')->nullable();
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('risk_category');
            $table->index('scanned_at');
            $table->index('domain');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
