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
        Schema::create('asientos_diarios', function (Blueprint $table) {
            $table->id();
            $table->string('nro_asiento')->nullable();
            $table->decimal('monto_debe', 18, 2);
            $table->decimal('monto_haber', 18, 2);
            $table->string('descripcion')->nullable();
            $table->foreignId('transaccion_id')->nullable()->constrained('transacciones')->onDelete('set null');
            $table->foreignId('cuenta_id')->nullable()->constrained('cuentas')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asientos_diarios');
    }
};
