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
        Schema::create('space_list_link', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('space_xid');
            $table->unsignedBigInteger('color_xid');
            $table->unsignedBigInteger('priority_xid');
            $table->string('list_name')->nullable();
            $table->string('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('is_active', [1, 0])->default(1)->comment('1=Active, 0=InActive');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('space_xid')->references('id')->on('space')->onDelete('cascade');
            $table->foreign('color_xid')->references('id')->on('colors')->onDelete('cascade');
            $table->foreign('priority_xid')->references('id')->on('priority_master')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('space_list_link');
    }
};
