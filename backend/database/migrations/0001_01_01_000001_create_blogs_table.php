<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Matches the existing table exactly (SHOW CREATE TABLE blogs), originally built by database/init.sql.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->foreign('user_id', 'fk_blogs_user')
                ->references('id')->on('users')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
