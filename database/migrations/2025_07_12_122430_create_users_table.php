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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('display_name')->nullable(); // tên hiển thị công khai
            $table->string('username')->unique()->nullable(); // tên đăng nhập (có thể dùng để đăng nhập)
            $table->string('email')->unique()->nullable(); // email người dùng (có thể dùng null, người dùng sẽ cập nhật sau khi có account)
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedTinyInteger('status')->default(0)->comment('0=unverified,1=active,2=blocked');
          
            // $table->enum('membership_type', ['free', 'premium', 'vip']) ->nullable()   // Membership type: chỉ áp dụng cho role member 
            $table->string('profile_image')->nullable(); // ảnh
            
            $table->timestamps();
            $table->rememberToken();

            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->softDeletes(); // hỗ trợ xóa mềm
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
