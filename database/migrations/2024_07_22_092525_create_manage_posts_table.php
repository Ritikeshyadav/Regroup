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
        Schema::create('manage_posts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('iam_principal_xid');
            $table->foreign('iam_principal_xid')->references('id')->on('iam_principal')->onDelete('cascade');

            $table->unsignedBigInteger('post_in');
            $table->foreign('post_in')->references('id')->on('manage_communities')->onDelete('cascade');

            $table->longText('caption')->nullable();
            $table->string('image')->nullable();
            $table->string('manage_tags_xids')->nullable();
            $table->enum('post_as',['Individual','Anonymous'])->nullable();
            $table->boolean('is_uploaded_by_bussiness_user')->default(0);
            $table->string('cta_title')->nullable();
            $table->string('cta_link')->nullable();

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
        Schema::dropIfExists('manage_posts');
    }
};
