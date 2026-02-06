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
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('business_unit');
            $table->string('credentials')->nullable();
            $table->rememberToken();
            $table->timestamps();
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

        // 🧩 Insert initial admin user
        DB::table('users')->insert([
            'name' => 'John Cannon Matucad',
            'email' => 'jcmatucad@desco.ph',
            'password' => Hash::make('admin'), // You can change this later
            'business_unit' => 'PIONEER1',
            'credentials' => 'SUPER_ADMIN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 🧩 Insert initial admin user
        DB::table('users')->insert([
            'name' => 'Carlos Verde',
            'email' => 'cverde@desco.ph',
            'password' => Hash::make('admin'), // You can change this later
            'business_unit' => 'PIONEER1',
            'credentials' => 'SUPER_ADMIN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
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
