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
        Schema::create('iam_principal_pinned_links', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('iam_principal_xid');
            $table->foreign('iam_principal_xid')->references('id')->on('iam_principal')->onDelete('cascade');

            $table->unsignedBigInteger('manage_tags_xid')->nullable();
            $table->foreign('manage_tags_xid')->references('id')->on('manage_tags')->onDelete('cascade');

            $table->unsignedBigInteger('manage_communities_xid')->nullable();
            $table->foreign('manage_communities_xid')->references('id')->on('manage_communities')->onDelete('cascade');
            
            $table->unsignedBigInteger('pin_iam_principal_xid')->nullable();
            $table->foreign('pin_iam_principal_xid')->references('id')->on('iam_principal')->onDelete('cascade');

            // $table->boolean('is_active')->default(1)->comment('1=Active, 0=InActive');
            // $table->integer('created_by')->nullable();
            // $table->integer('modified_by')->nullable();
            $table->softDeletes();        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iam_principal_pinned_links');
    }
};
