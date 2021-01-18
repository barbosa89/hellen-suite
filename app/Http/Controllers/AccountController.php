<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdatePassword;

class AccountController extends Controller
{
    /**
     * Verify team member email.
     *
     * @param  string  $email
     * @return string  $token
     */
    public function verify(string $email = null, string $token = null)
    {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL);
        $token = param_clean($token);

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

        return redirect(url('/'));
    }

    /**
     * Show form to change password.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePassword()
    {
        return view('app.accounts.password');
    }

    /**
     * Update User password.
     *
     * @param UpdatePassword $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(UpdatePassword $request)
    {
        if (Hash::check($request->password, auth()->user()->password)) {
            auth()->user()->update(['password' => bcrypt($request->new_password)]);

            flash(trans('accounts.password.updated'))->success();

            return redirect()->route('home');
        }

        flash(trans('accounts.password.wrong'))->error();

        return back();
    }
}
