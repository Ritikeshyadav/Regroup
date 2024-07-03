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
        Schema::create('iam_app_resource_action_link', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_resource_xid');
            $table->unsignedBigInteger('app_action_xid');
            $table->enum('is_active', [1, 0])->default(1)->comment('1=Active, 0=InActive');
            $table->integer('created_by')->nullable();
            $table->integer('modified_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('app_resource_xid')->references('id')->on('iam_app_resource')->onDelete('cascade');
            $table->foreign('app_action_xid')->references('id')->on('iam_app_action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iam_app_resource_action_link');
    }
};
