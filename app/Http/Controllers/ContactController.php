<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageContact;
use App\Mail\ContactMessage;
use Illuminate\Support\Facades\Mail;

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
        Mail::to(config('mail.from.address'))
            ->send(new ContactMessage($request));

        if (Mail::failures()) {
            flash()->overlay(trans('email.fail'), trans('common.sorry'));
        } else {
            flash()->overlay(trans('email.sent'), trans('common.great'));
        }

        return redirect('/');
    }
}
