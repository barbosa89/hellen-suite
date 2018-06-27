<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\Activate;

class AccountController extends Controller
{
    public function activate($email = '', $token = '')
    {
        $email = htmlentities($email, ENT_QUOTES);
        $token = htmlentities($token, ENT_QUOTES);
        
        $user = User::where('email', $email)
            ->where('token', $token)
            ->where('verified', false)
            ->where('status', false)
            ->first(['id', 'email', 'token', 'verified', 'status']);

        if (empty($user)) {
            flash()->overlay(trans('common.error'), 'Error');

            return back();
        }

        $user->token = null;
        $user->verified = true;
        $user->status = true;

        if ($user->update()) {
            flash()->overlay(trans('common.accountWasActivated'), trans('common.great'));

            return redirect()->route('login');
        }

        flash()->overlay(trans('common.error'), 'Error');

        return back();
    }  
    
    public function showFormActivation()
    {
        return view('auth.activation');
    }

    public function activation(Activate $request)
    {       
        $user = User::where('email', $request->email)
            ->where('token', $request->token)
            ->where('verified', false)
            ->where('status', false)
            ->first(['id', 'email', 'token', 'verified', 'status']);

        if (empty($user)) {
            flash()->overlay(trans('common.error'), 'Error');

            return back();
        }

        $user->token = null;
        $user->verified = true;
        $user->status = true;

        if ($user->update()) {
            flash()->overlay(trans('common.accountWasActivated'), trans('common.great'));

            return redirect()->route('login');
        }

        flash()->overlay(trans('common.error'), 'Error');

        return back();
    }
}
