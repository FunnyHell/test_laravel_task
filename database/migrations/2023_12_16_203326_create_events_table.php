<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('text');
            $table->timestamp('creation_date')->useCurrent();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Откат миграции - удаление таблицы `events`.
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
