<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */

     /**
     * Created By : Hritik
     * Created At : 09 July 2024
     * Use : To create table for Logged in user with business data
     */
    public function up(): void
    {
        Schema::create('iam_principal_business_user_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('iam_principal_xid');         
            $table->foreign('iam_principal_xid')->references('id')->on('iam_principal')->onDelete('cascade');

            $table->unsignedBigInteger('business_type_xid');         
            $table->foreign('business_type_xid')->references('id')->on('business_types')->onDelete('cascade');

            $table->string('business_owner_name')->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_username')->nullable();
            $table->date('founded_on')->nullable();
            $table->string('bio')->nullable();
            $table->string('business_location')->nullable();
            $table->string('business_profile_image')->nullable();
            $table->string('business_contact_number')->nullable();
            $table->string('business_email')->nullable();
            $table->string('business_handle')->nullable();
            $table->time('opening_hours')->nullable();
            $table->string('website_link')->nullable();
            $table->string('google_review_link')->nullable();
            $table->string('tags')->nullable();
            $table->string('business_logo')->nullable();

            $table->string('banner_image')->nullable();

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
        Schema::dropIfExists('iam_principal_business_user_links');
    }
};
