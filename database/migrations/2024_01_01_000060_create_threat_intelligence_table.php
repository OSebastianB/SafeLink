<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threat_intelligence', function (Blueprint $table) {
            $table->id();
            $table->string('domain');
            $table->string('url_hash', 64)->nullable();
            $table->string('status', 40);
            $table->string('threat_type', 60)->nullable();
            $table->string('source');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_synthetic')->default(false);
            $table->timestamps();

            $table->index('domain');
            $table->index('status');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threat_intelligence');
    }
};
