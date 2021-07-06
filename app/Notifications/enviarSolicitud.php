<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Support\Facades\DB;

class enviarSolicitud extends Notification implements ShouldQueue
{
    use Queueable;

    protected $token;
    protected $idSolicitud;
    protected $solicitante;
    protected $labor;
    protected $tipo;
    protected $validador;
    protected $sede;
    protected $empresa;
    protected $tipoVi;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token,$idSolicitud,$solicitante,$labor,$tipo,$validador,$sede,$empresa,$tipoVi)
    {
        $this->token = $token;
        $this->idSolicitud = $idSolicitud;
        $this->solicitante = $solicitante;
        $this->labor = $labor;
        $this->tipo = $tipo;                    //indicará si el correo es para los del flujo o el solicitante
        $this->validador = $validador;          //indicará quien ha validado la solicitud
        $this->sede = $sede;                    //Para obtener el nivel del validador
        $this->empresa = $empresa;              //Para obtener el nivel del validador
        $this->tipoVi = $tipoVi;                //Para obtener el nivel del validador
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {   //Ruta de estructura de mail resources/views/vendor/notifications/email.blade.php
        //$url = route('reset.token'.$this->token);
         $url = url($this->token);

         //muestra hasta 5 colaboradores
         $arrayColabora = array();
         $colaboradores = DB::table('ohxqc_documentos_solicitud')->select('nombre')->where('solicitud_id',$this->idSolicitud)->limit(5)->get();
         $i = 0;
         foreach($colaboradores as $colabora){
            $arrayColabora[$i] = $colabora->nombre;
            $i++;
         }
         $listado = implode(',', $arrayColabora);

       
        if($this->tipo == 1){
            if(strlen($this->validador) > 0){
                $nivelValidador = DB::table('ohxqc_config_solicitud_empresas')
                ->select('nivel')
                ->where('usuario_aprobador_id', auth()->user()->id)
                ->where('tipo_visitante', $this->tipoVi)
                ->where('sede_id', $this->sede)
                ->where('empresa_id', $this->empresa)
                ->get();
       
                $flujoDe = $nivelValidador[0]->nivel;
       
                $nivelSolicitud = DB::table('ohxqc_solicitud_por_aprobar')
                ->select('niveles')
                ->where('id_solicitud', $this->idSolicitud)
                ->where('tipo_visitante', $this->tipoVi)
                ->where('sede_id', $this->sede)
                ->get();
       
                $flujoHasta = $nivelSolicitud[0]->niveles;
       
                return (new MailMessage)
                ->subject('Solicitud #'. $this->idSolicitud.' para aprobar nuevo visitante')
                ->greeting('Hola')
                ->line('Según el flujo al que perteneces, has recibido este correo para poder validar la solicitud número: '.$this->idSolicitud)
                ->line('Solicitante: '.$this->solicitante)
                ->line('Labor a realizar: '.$this->labor)
                ->line('Integrantes: '.$listado."...")
                ->line('Flujo validado: '.$flujoDe.'/'.$flujoHasta)
                ->line('Validada por: '.auth()->user()->name.' en el flujo #'.$flujoDe)
                ->line('Para ver mas detalles, pulsa a continuación:')
                ->action('Validar solicitud ', url($url))
                ->salutation('Cordialmente:');
            }else{
                return (new MailMessage)
                ->subject('Solicitud #'. $this->idSolicitud.' para aprobar nuevo visitante')
                ->greeting('Hola')
                ->line('Según el flujo al que perteneces, has recibido este correo para poder validar la solicitud número: '.$this->idSolicitud)
                ->line('Solicitante: '.$this->solicitante)
                ->line('Labor a realizar: '.$this->labor)
                ->line('Integrantes: '.$listado."...")
                ->line('Para ver mas detalles, pulsa a continuación:')
                ->action('Validar solicitud ', url($url))
                ->salutation('Cordialmente:');
            }
        }else if($this->tipo == 2){
            return (new MailMessage)
            ->subject('Solicitud #'. $this->idSolicitud.' Enviada a Aprobación')
            ->greeting('Hola')
            ->line('Su solicitud de número: '.$this->idSolicitud.', ha sido enviada para la respectiva validación; le estaremos notificando por este mismo medio el estado de la misma.')
            ->line('Solicitante: '.$this->solicitante)
            ->line('Labor a realizar: '.$this->labor)
            ->salutation('Cordialmente:');
        }else{
            $nivelValidador = DB::table('ohxqc_config_solicitud_empresas')
            ->select('nivel')
            ->where('usuario_aprobador_id', auth()->user()->id)
            ->where('tipo_visitante', $this->tipoVi)
            ->where('sede_id', $this->sede)
            ->where('empresa_id', $this->empresa)
            ->get();
   
            $flujoDe = $nivelValidador[0]->nivel;
            $siguiente = $flujoDe + 1;
   
            $nivelSolicitud = DB::table('ohxqc_solicitud_por_aprobar')
            ->select('niveles')
            ->where('id_solicitud', $this->idSolicitud)
            ->where('tipo_visitante', $this->tipoVi)
            ->where('sede_id', $this->sede)
            ->get();
   
            $flujoHasta = $nivelSolicitud[0]->niveles;

            $nombreSede = DB::table('ohxqc_ubicaciones')->select('descripcion')->where('id_ubicacion', $this->sede)->get();

            $nombreSede = $nombreSede[0]->descripcion;
   
            return (new MailMessage)
            ->subject('Notificación Solicitud #'. $this->idSolicitud.' - '.$nombreSede)
            ->greeting('Hola')
            ->line('Su solicitud de número: '.$this->idSolicitud.', ha sido validada en el flujo #'.$flujoDe.' por '.auth()->user()->name.' y enviada al flujo #'.$siguiente.'.')
            ->line('Flujo actual: '.$flujoDe.'/'.$flujoHasta)
            ->salutation('Cordialmente:');
        }
      
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
