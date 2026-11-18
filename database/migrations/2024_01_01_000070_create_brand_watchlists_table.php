<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_watchlists', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name');
            $table->string('official_domain');
            $table->json('keywords')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('official_domain');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_watchlists');
    }
};
