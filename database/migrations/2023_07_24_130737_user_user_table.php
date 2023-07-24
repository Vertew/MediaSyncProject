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
        Schema::create('user_user', function (Blueprint $table) {
            $table->primary(['user1_id','user2_id']);
            $table->bigInteger('user1_id')->unsigned();
            $table->bigInteger('user2_id')->unsigned();

            $table->foreign('user1_id')->references('id')->on('users')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('user2_id')->references('id')->on('users')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_user');
    }
};
