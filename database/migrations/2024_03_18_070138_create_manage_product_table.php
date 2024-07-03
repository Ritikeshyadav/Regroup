<?php

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
        Schema::create('manage_product', function (Blueprint $table) {
            $table->id();
            $table->string('product_name')->nullable();
            $table->text('product_description')->nullable();
            $table->string('image')->nullable();
            $table->integer('product_monthly_price')->nullable();
            $table->integer('product_yearly_price')->nullable();
            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_monthly_price_id')->nullable();
            $table->string('stripe_yearly_price_id')->nullable();
            $table->boolean('is_popular')->default(0)->comment('1=true, 0=false');
            $table->integer('created_by')->nullable();
            $table->integer('modified_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manage_product');
    }
};
