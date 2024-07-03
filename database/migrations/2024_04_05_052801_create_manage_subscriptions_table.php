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
        Schema::create('manage_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('iam_principal_xid');
            $table->string('stripe_customer_id')->nullable();
            $table->string('product_stripe_id')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->integer('manage_product_xid')->nullable();
            $table->integer('manage_product_price_xid')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->integer('amount')->nullable();
            $table->dateTime('subscription_date')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('paused_at')->nullable();
            $table->boolean('is_cancelled')->nullable();
            $table->boolean('is_paused')->nullable();
            $table->enum('is_active', [1, 0])->default(1)->comment('1=Active, 0=InActive');
            $table->integer('created_by')->nullable();
            $table->integer('modified_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('iam_principal_xid')->references('id')->on('iam_principal')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manage_subscriptions');
    }
};
