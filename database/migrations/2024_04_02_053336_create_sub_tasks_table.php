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
        Schema::create('sub_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('space_folder_list_task_link_xid');
            $table->unsignedBigInteger('iam_principal_xid');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('priority_master_xid');
            $table->string('comment')->nullable();
            $table->unsignedBigInteger('status_master_xid');
            $table->string('is_group_assignee')->nullable();
            $table->enum('is_active', [1, 0])->default(1)->comment('1=Active, 0=InActive');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('space_folder_list_task_link_xid')->references('id')->on('space_folder_list_task_links')->onDelete('cascade');
            $table->foreign('iam_principal_xid')->references('id')->on('iam_principal')->onDelete('cascade');
            $table->foreign('status_master_xid')->references('id')->on('status_master')->onDelete('cascade');
            $table->foreign('priority_master_xid')->references('id')->on('priority_master')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_tasks');
    }
};
