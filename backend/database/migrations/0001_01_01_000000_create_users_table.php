<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Matches the existing table exactly (SHOW CREATE TABLE users), originally built by database/init.sql.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id', true); // signed INT AUTO_INCREMENT PRIMARY KEY
            $table->string('name', 100);
            $table->string('email', 150)->unique('email');
            $table->string('password');
            $table->string('image')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
