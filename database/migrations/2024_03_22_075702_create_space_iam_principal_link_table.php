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
        Schema::create('space_iam_principal_link', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('space_xid');
            $table->unsignedBigInteger('iam_principal_xid');
            $table->date('date')->nullable();
            $table->enum('is_active', [1, 0])->default(1)->comment('1=Active, 0=InActive');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('space_xid')->references('id')->on('space')->onDelete('cascade');
            $table->foreign('iam_principal_xid')->references('id')->on('iam_principal')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('space_iam_principal_link');
    }
};
