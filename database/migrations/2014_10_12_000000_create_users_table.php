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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('login')->unique();
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name');
            $table->timestamp('registration_date')->useCurrent();
            $table->date('birthdate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Откат миграции - удаление таблицы `users`.
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
