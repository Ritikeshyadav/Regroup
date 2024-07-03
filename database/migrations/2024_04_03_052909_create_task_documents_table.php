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
        Schema::create('task_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('iam_principal_xid');
            $table->unsignedBigInteger('space_folder_list_task_link_xid');
            $table->string('doc_name')->nullable();
            $table->string('file')->nullable();
            $table->enum('is_active', [1, 0])->default(1)->comment('1=Active, 0=InActive');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('iam_principal_xid')->references('id')->on('iam_principal')->onDelete('cascade');
            $table->foreign('space_folder_list_task_link_xid')->references('id')->on('space_folder_list_task_links')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_documents');
    }
};
