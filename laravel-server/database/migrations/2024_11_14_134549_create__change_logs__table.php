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
        Schema::create('change_logs2', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');  // Тип сущности (например, User, Role, Permission)
            $table->unsignedBigInteger('entity_id');  // ID сущности
            $table->text('old_values');  // Старые значения сущности
            $table->text('new_values');  // Новые значения сущности
            $table->unsignedBigInteger('user_id');  // ID пользователя, который совершил изменение
            $table->enum('action', ['created', 'updated', 'deleted']);  // Действие с сущностью
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_logs2');
    }
};
