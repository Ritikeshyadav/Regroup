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
        Schema::create('manage_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manage_community_xid');
            $table->foreign('manage_community_xid')->references('id')->on('manage_communities')->onDelete('cascade');
            
            $table->string('name')->nullable();
            $table->boolean('is_requested')->default(0)->comment('1=true, 0=false');
            $table->boolean('is_accepted')->default(0)->comment('1=true, 0=false');
            
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
        Schema::dropIfExists('manage_tags');
    }
};
