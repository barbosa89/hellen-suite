<?php

namespace App\Notifications;

use App\User;
use App\Models\Hotel;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyTeamMemberEmail extends Notification
{
    use Queueable;

    /**
     * The new team member
     *
     * @var App\User
     */
    public $user;

    /**
     * The hotel headquarters
     *
     * @var App\Models\Hotel
     */
    public $hotel;

    /**
     * Temporary password
     *
     * @var string
     */
    public $password;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Hotel $hotel, string $password)
    {
        $this->user = $user;
        $this->hotel = $hotel;
        $this->password = $password;
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
    {
        $url = URL::temporarySignedRoute(
            'account.verify',
            now()->addDay(1),
            [
                'email' => $this->user->email,
                'token' => $this->user->token
            ]
        );

        return (new MailMessage)
                    ->subject('Verificación de correo electrónico')
                    ->greeting('Hola, ' . $this->user->name)
                    ->line($this->hotel->business_name . ' te ha agregado como miembro de su equipo.')
                    ->line('Tu contraseña temporal es: ' . $this->password)
                    ->line('Por favor, haz clic en el siguiente enlace para verificar tu correo.')
                    ->action('Verificar correo', $url)
                    ->line('Gracias por ser parte de ' . config('app.name'));
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
