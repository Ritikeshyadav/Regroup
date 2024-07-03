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
        Schema::create('sub_task_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('iam_principal_xid');
            $table->unsignedBigInteger('sub_task_xid');
            $table->string('doc_name')->nullable();
            $table->string('file')->nullable();
            $table->enum('is_active', [1, 0])->default(1)->comment('1=Active, 0=InActive');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('iam_principal_xid')->references('id')->on('iam_principal')->onDelete('cascade');
            $table->foreign('sub_task_xid')->references('id')->on('sub_tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_task_documents');
    }
};
