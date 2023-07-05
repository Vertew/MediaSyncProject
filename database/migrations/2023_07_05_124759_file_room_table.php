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
        Schema::create('file_room', function (Blueprint $table) {
            $table->primary(['room_id','file_id']);
            $table->bigInteger('room_id')->unsigned();
            $table->bigInteger('file_id')->unsigned();

            $table->foreign('room_id')->references('id')->on('rooms')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('file_id')->references('id')->on('files')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->timestamp('added_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_room');
    }
};
