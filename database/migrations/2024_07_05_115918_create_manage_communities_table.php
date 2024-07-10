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
        Schema::create('manage_communities', function (Blueprint $table) {
            $table->id();
            $table->string('community_profile_photo')->nullable();
            $table->string('community_banner_image')->nullable();
            $table->string('community_name')->nullable();
            $table->string('community_location')->nullable();
            $table->longText('community_description')->nullable();
            $table->unsignedBigInteger('community_type_xid');
            $table->unsignedBigInteger('activity_xid');
            $table->foreign('community_type_xid')->references('id')->on('manage_community_types')->onDelete('cascade');
            $table->foreign('activity_xid')->references('id')->on('manage_activities')->onDelete('cascade');
            $table->boolean('is_active')->default(1)->comment('1=Active, 0=InActive');
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
        Schema::dropIfExists('manage_communities');
    }
};
