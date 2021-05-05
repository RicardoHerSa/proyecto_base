<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClInfoAdicionalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_info_adicional', function (Blueprint $table) {
            $table->id('id_info_add');
            $table->string('forma_pago');
            $table->string('metodo_pago');
            $table->string('cfdi');
            $table->string('contacto_entrega')->nullable();
            $table->char('cuenta_pago', 4)->nullable();
            $table->string('horario_dia')->nullable();
            $table->char('requiere_oc', 1)->nullable();
            $table->char('requiere_anexos', 1)->nullable();
            $table->string('tipo_anexos')->nullable();
            $table->char('factura_portal_cliente', 1)->nullable();
            $table->char('manual_portal_cliente', 1)->nullable();
            $table->char('ult_dia_ingreso', 1)->nullable();
            $table->integer('usuario_id');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_info_adicionals');
    }
}
