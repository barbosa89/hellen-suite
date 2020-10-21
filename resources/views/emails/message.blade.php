@extends('emails.layouts.main')

@section('content')

    <!-- 1 Column Text + Button : BEGIN -->
    <tr>
        <td bgcolor="#ffffff" style="padding: 40px 40px 20px; text-align: center;">
            <h1 style="margin: 0; font-family: sans-serif; font-size: 24px; line-height: 27px; color: #333333; font-weight: normal;">
                @lang('email.notification')
            </h1>
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; text-align: justify;">
            <p style="margin: 0;">
                @lang('common.name'): {{ $name }}<br>
                @lang('common.email'): {{ $email }}<br>
                @lang('common.phone'): {{ $phone }}<br>
            </p>
            <p style="margin: 0;text-align:justify">
                @lang('notes.content') <br><br>
                {{ $msg }}
            </p>
        </td>
    </tr>
        <!-- 1 Column Text + Button : END -->

    <!-- Clear Spacer : BEGIN -->
    <tr>
        <td height="40" style="font-size: 0; line-height: 0;">
            &nbsp;
        </td>
    </tr>
    <!-- Clear Spacer : END -->

@endsection
