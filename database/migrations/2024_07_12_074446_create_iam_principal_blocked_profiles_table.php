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
        Schema::create('iam_principal_blocked_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('iam_principal_xid');
            $table->foreign('iam_principal_xid')->references('id')->on('iam_principal')->onDelete('cascade');
            $table->unsignedBigInteger('blocked_iam_principal_xid');
            $table->foreign('blocked_iam_principal_xid')->references('id')->on('iam_principal')->onDelete('cascade');
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
        Schema::dropIfExists('iam_principal_blocked_profiles');
    }
};