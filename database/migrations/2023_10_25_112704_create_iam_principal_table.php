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
        Schema::create('iam_principal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('principal_type_xid');
            $table->unsignedBigInteger('principal_source_xid');
            $table->string('google_id')->nullable();
            $table->string('apple_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('microsoft_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('password_hash')->nullable();
            $table->string('pin', 4)->nullable();
            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('other_phone_number', 15)->nullable();
            $table->string('email_address', 50)->nullable();
            $table->string('address_line1', 255)->nullable();
            $table->string('address_line2', 255)->nullable();
            $table->bigInteger('city_xid')->nullable();
            $table->bigInteger('state_xid')->nullable();
            $table->bigInteger('country_xid')->nullable();
            $table->string('post_code', 10)->nullable();
            $table->dateTime('last_login_datetime')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('referral_code')->nullable();
            $table->string('description')->nullable();
            $table->enum('is_active', [1, 0])->default(1)->comment('1=Active, 0=InActive');
            $table->integer('created_by')->nullable();
            $table->integer('modified_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('principal_type_xid')->references('id')->on('iam_principal_type')->onDelete('cascade');
            $table->foreign('principal_source_xid')->references('id')->on('iam_principal_source')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iam_principal');
    }
};
