<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ForeignKeyDefinition;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cuentas_orgs', function (Blueprint $table) {
            $table->id();
            $table->unique(['cuenta_id','organizacion_id']);
            $table->foreignId('cuenta_id')->nullable()->constrained('cuentas')->onDelete('set null');
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_orgs');
    }
};
