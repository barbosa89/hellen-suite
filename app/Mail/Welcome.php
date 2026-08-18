<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Welcome extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        private User $user,
        private $password
    ) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->user->load([
            'roles' => function ($query): void {
                $query->select('id', 'name');
            },
            'father' => function ($query): void {
                $query->select('id', 'name');
            },
        ]);

        return $this->view('emails.welcome.'.$this->user->roles->first()->name)
            ->subject(trans('email.active'))
            ->with([
                'user' => $this->user,
                'password' => $this->password,
            ]);
    }
}
