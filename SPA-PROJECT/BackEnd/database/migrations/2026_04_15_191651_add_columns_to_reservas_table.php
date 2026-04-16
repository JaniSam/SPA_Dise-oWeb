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
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->integer('especialista_id')->nullable();
            $table->integer('monto_total')->nullable();
            $table->string('metodo_pago')->nullable();
            $table->string('estado')->default('pending');
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
