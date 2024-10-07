<?php

use App\Enums\ProductCategoryEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('category', ProductCategoryEnum::getValues())
                ->default(ProductCategoryEnum::NORMAL);
            $table->integer('min_quality')->default(0);
            $table->integer('max_quality')->default(50);
            $table->integer('daily_decrease')->default(1);
            $table->integer('sell_in_threshold')->nullable(); // E.g., when quality change accelerates
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_rules');
    }
};
