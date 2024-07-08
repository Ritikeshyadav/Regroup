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
        Schema::create('manage_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manage_group_type_xid');
            $table->string('title');
            $table->string('background_image')->nullable();
            $table->string('group_image')->nullable();
            $table->string('location');
            $table->string('link');
            $table->longText('description');
            $table->boolean('is_active',)->default(1)->comment('1=Active, 0=InActive');
            $table->foreign('manage_group_type_xid')->references('id')->on('manage_group_types')->onDelete('cascade');
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
        Schema::dropIfExists('manage_groups');
    }
};
