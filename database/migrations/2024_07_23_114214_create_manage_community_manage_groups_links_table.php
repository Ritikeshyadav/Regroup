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
        Schema::create('manage_community_and_groups_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('iam_principal_xid');
            $table->foreign('iam_principal_xid')->references('id')->on('iam_principal')->onDelete('cascade');

            $table->unsignedBigInteger('manage_community_xid');
            $table->foreign('manage_community_xid')->references('id')->on('manage_communities')->onDelete('cascade');

            $table->unsignedBigInteger('manage_group_xid');
            $table->foreign('manage_group_xid')->references('id')->on('manage_groups')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manage_community_manage_groups_links');
    }
};
