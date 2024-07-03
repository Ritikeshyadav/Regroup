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
        Schema::create('iam_principal_type', function (Blueprint $table) {
            $table->id();
            $table->string('principal_type_title',255)->nullable();
            $table->enum('is_active', [1, 0])->default(1)->comment('1=Active, 0=InActive');
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
        Schema::dropIfExists('iam_principal_type');
    }
};
