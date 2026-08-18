<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageContact;
use App\Mail\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\SendFailedException;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Send a email to owner blog.
     *
     * @param  \App\Http\Requests\ContactEmail  $request
     * @return \Illuminate\Http\Response
     */
    public function message(SendMessageContact $request)
{
    try {
        Mail::to(config('mail.from.address'))->send(new ContactMessage($request));

        flash()->overlay(trans('email.sent'), trans('common.great'));
    } catch (SendFailedException $e) {
        flash()->overlay(trans('email.fail'), trans('common.sorry'));
        Log::error('Error al enviar el correo: ' . $e->getMessage());
    }

    return redirect('/');
}
}
