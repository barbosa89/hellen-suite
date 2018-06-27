<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Welcome extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The recipient user.
     *
     * @var User
     */
    private $user;

    /**
     * Temporal password.
     *
     * @var string
     */
    private $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->user->load([
            'roles' => function ($query) {
                $query->select('id', 'name');
            },
            'parent' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        return $this->view('emails.welkome.' . $this->user->roles->first()->name)
            ->subject(trans('email.active'))
            ->with([
                'user' => $this->user,
                'password' => $this->password,
            ]);
    }
}
