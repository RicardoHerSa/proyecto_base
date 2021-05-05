<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetRequest extends Notification implements ShouldQueue
{
    use Queueable;

    protected $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
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
        $url = url('/api/resetemail/find/'.$this->token);
        return (new MailMessage)
            ->subject('Solicitud de reestablecimiento de contraseña')
            ->greeting('Hola')
            ->line('Recibiste este correo porque solicitaste restablecer la contraseña de tu cuenta de usuario en el portal del colaborador ')
            ->line('Para hacerlo, haz clic en este botón :')
            ->action('Restablecer la contraseña', url($url))
            ->line('Si no realizaste ninguna solicitud  de restablecimiento de contraseña, haz caso omiso a este mensaje.')
            ->salutation('Cordialmente:');
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
