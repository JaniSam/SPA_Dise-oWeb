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
    Schema::create('usuarios', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('apellido');
        $table->string('email')->unique();
        $table->string('telefono')->nullable();
        $table->string('password');
        $table->integer('rol_id')->default(4); // Por defecto rol cliente/usuario
        $table->boolean('activo')->default(true);
        $table->timestamps();
    });
}
};
