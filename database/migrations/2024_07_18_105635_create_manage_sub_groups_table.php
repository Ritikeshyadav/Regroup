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
        Schema::create('manage_sub_groups', function (Blueprint $table) {
            $table->id();
           
            $table->unsignedBigInteger('manage_group_xid');
            $table->unsignedBigInteger('manage_group_type_xid');

            $table->string('title')->nullable();
            $table->string('background_image')->nullable();
            $table->string('sub_group_image')->nullable();
            $table->string('location')->nullable();
            $table->string('link')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('is_active',)->default(1)->comment('1=Active, 0=InActive');
            $table->foreign('manage_group_type_xid')->references('id')->on('manage_group_types')->onDelete('cascade');
            $table->foreign('manage_group_xid')->references('id')->on('manage_groups')->onDelete('cascade');

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
        Schema::dropIfExists('manage_sub_groups');
    }
};
