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
        Schema::create('manage_privacy_policies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('iam_principal_type_xid');
            $table->foreign('iam_principal_type_xid')->references('id')->on('iam_principal_type')->onDelete('cascade');
            $table->longText('content')->nullable();
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
        Schema::dropIfExists('manage_privacy_policies');
    }
};
