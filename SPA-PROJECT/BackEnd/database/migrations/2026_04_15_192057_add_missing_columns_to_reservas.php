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
    Schema::table('reservas', function (Blueprint $table) {
        if (!Schema::hasColumn('reservas', 'titulo')) {
            $table->string('titulo')->nullable();
        }
        if (!Schema::hasColumn('reservas', 'fecha_inicio')) {
            $table->dateTime('fecha_inicio')->nullable();
        }
        if (!Schema::hasColumn('reservas', 'fecha_fin')) {
            $table->dateTime('fecha_fin')->nullable();
        }
        if (!Schema::hasColumn('reservas', 'especialista_id')) {
            $table->integer('especialista_id')->nullable();
        }
        if (!Schema::hasColumn('reservas', 'monto_total')) {
            $table->integer('monto_total')->nullable();
        }
        if (!Schema::hasColumn('reservas', 'metodo_pago')) {
            $table->string('metodo_pago')->nullable();
        }
        if (!Schema::hasColumn('reservas', 'estado')) {
            $table->string('estado')->default('pending');
        }
        if (!Schema::hasColumn('reservas', 'observaciones')) {
            $table->text('observaciones')->nullable();
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            //
        });
    }
};
