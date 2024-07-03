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
        Schema::create('space', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icon_xid');
            $table->unsignedBigInteger('color_xid');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->enum('is_private', [1, 0])->default(1)->comment('1=Active, 0=InActive');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('icon_xid')->references('id')->on('icon_master')->onDelete('cascade');
            $table->foreign('color_xid')->references('id')->on('colors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('space');
    }
};
