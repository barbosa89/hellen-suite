<?php

namespace App\Notifications;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyTeamMemberEmail extends Notification
{
    use Queueable;

    public User $user;

    public Hotel $hotel;

    public string $password;

    public function __construct(User $user, Hotel $hotel, string $password)
    {
        $this->user = $user;
        $this->hotel = $hotel;
        $this->password = $password;
    }

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'accounts.verify',
            now()->addDay(),
            [
                'email' => $this->user->email,
                'token' => $this->user->token,
            ]
        );

        return (new MailMessage)
            ->subject('Verificación de correo electrónico')
            ->greeting('Hola, '.$this->user->name)
            ->line($this->hotel->business_name.' te ha agregado como miembro de su equipo.')
            ->line('Tu contraseña temporal es: '.$this->password)
            ->line('Por favor, haz clic en el siguiente enlace para verificar tu correo.')
            ->action('Verificar correo', $url)
            ->line('Gracias por ser parte de '.config('app.name'));
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            //
        ];
    }
}
