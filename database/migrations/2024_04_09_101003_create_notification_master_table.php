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
        Schema::create('notification_master', function (Blueprint $table) {
            $table->id();
            $table->integer('iam_principal_xid');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('is_read', [1, 0])->default(1)->comment('1=Active, 0=InActive');
            $table->enum('is_active', [1, 0])->default(1)->comment('1=Active, 0=InActive');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_master');
    }
};
