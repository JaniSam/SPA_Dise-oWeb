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
    Schema::create('reservas', function (Blueprint $table) {
        $table->id();
        $table->string('titulo'); // Nombre del cliente
        $table->dateTime('fecha_inicio');
        $table->dateTime('fecha_fin');
        $table->integer('especialista_id');
        $table->integer('monto_total');
        $table->string('metodo_pago');
        $table->string('estado')->default('pending');
        $table->text('observaciones')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
