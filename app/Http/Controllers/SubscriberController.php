<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriber;
use Spatie\Newsletter\Facades\Newsletter;

class SubscriberController extends Controller
{
    /**
     * Store a newly created subscriber in MailChimp API.
     *
     * @return \Illuminate\Http\Response
     */
    public function subscribe(StoreSubscriber $request)
    {
        if (! Newsletter::isSubscribed($request->email)) {
            Newsletter::subscribePending($request->email);

            flash()->overlay(trans('landing.subscribers.pending'), trans('landing.subscribers.title'));
        } else {
            flash()->overlay(trans('landing.subscribers.exists'), trans('landing.subscribers.title'));
        }

        return redirect('/');
    }

    /**
     * Unsubscribe a subscriptor in MailChimp API.
     *
     * @param  string  $mail
     * @return \Illuminate\Http\Response
     */
    public function unsubscribe($email)
    {
        if (Newsletter::isSubscribed($email)) {
            Newsletter::unsubscribe($email);

            flash()->overlay(trans('landing.subscribers.leaves'), trans('landing.subscribers.title'));
        } else {
            flash()->overlay(trans('landing.subscribers.unknown'), trans('landing.subscribers.title'));
        }

        return redirect('/');
    }
}
