<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClInfoGeneralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_info_general', function (Blueprint $table) {
            $table->id('id_info');
            $table->date('fecha_diligencia');
            $table->string('num_documento', 12);
            $table->string('nombre_razon_social', 100);
            $table->string('pep')->nullable();
            $table->string('registro_mercantil')->nullable();
            $table->integer('actividad_comercial_id')->nullable();
            $table->char('grupo_empresarial_economico', 2)->nullable();
            $table->string('nombre_grupo', 100)->nullable();
            $table->string('web', 120)->nullable();
            $table->string('email_contacto_compras')->nullable()->unique();
            $table->string('nombre_contacto_compras', 100)->nullable();
            $table->string('email_contacto_tesoreria')->nullable()->unique();
            $table->string('nombre_contacto_tesoreria', 100)->nullable();
            $table->string('email_recibir_factura')->nullable()->unique();
            $table->string('forma_recibir_factura', 6)->nullable();
            $table->date('fecha_limite')->nullable();
            $table->char('realiza_operaciones', 2)->nullable();
            $table->integer('tipo_id_tributaria')->nullable();
            $table->integer('tipo_persona')->nullable();
            $table->integer('pais_id')->nullable();
            $table->char('estado', 10)->nullable();
            $table->integer('id_user_credito')->nullable();
            $table->integer('id_user_comercial')->nullable();
            $table->timestamp('fecha_asignacion')->nullable();
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
        Schema::dropIfExists('cl_info_general');
    }
}
