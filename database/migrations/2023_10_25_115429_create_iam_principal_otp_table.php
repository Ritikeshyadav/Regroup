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
        Schema::create('iam_principal_otp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('principal_xid');
            $table->string('otp_code')->nullable();
            $table->string('otp_purpose')->nullable();
            $table->dateTime('valid_till')->nullable();
            $table->boolean('is_used')->nullable();
            $table->enum('is_active', [1, 0])->default(1)->comment('1=Active, 0=InActive');
            $table->integer('created_by')->nullable();
            $table->integer('modified_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('principal_xid')->references('id')->on('iam_principal')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iam_principal_otp');
    }
};
