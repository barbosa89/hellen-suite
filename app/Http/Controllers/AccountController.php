<?php

namespace App\Http\Controllers;

use App\User;
use App\Helpers\Input;
use Carbon\Carbon;
class AccountController extends Controller
{
    /**
     * Verify team member email.
     *
     * @param  string  $email
     * @return string  $token
     */
    public function verify($email = '', $token = '')
    {
        $email = Input::clean($email);
        $token = Input::clean($token);

        $user = User::where('email', $email)
            ->where('token', $token)
            ->where('email_verified_at', null)
            ->first(['id', 'email', 'token', 'email_verified_at']);

        if (empty($user)) {
            flash()->overlay(trans('common.error'), 'Error');

            return back();
        }

        $user->token = null;
        $user->email_verified_at = Carbon::now()->toDateTimeString();

        if ($user->save()) {
            flash()->overlay(trans('common.accountWasActivated'), trans('common.great'));

            return redirect()->route('login');
        }

        flash()->overlay(trans('common.error'), 'Error');

        return redirect(utl('/'));
    }
}
