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
        Schema::create('space_folder_link', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('space_xid');
            $table->string('folder_name')->nullable();
            $table->enum('is_active', [1, 0])->default(1)->comment('1=Active, 0=InActive');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('space_xid')->references('id')->on('space')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('space_folder_link');
    }
};
