<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ trans('email.notification') }} | {{ config('app.name') }}</title> 
    <style>
        body {
	        margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }

        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        div[style*="margin: 16px 0"] {
            margin:0 !important;
        }

        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        table table table {
            table-layout: auto;
        }

        img {
            -ms-interpolation-mode:bicubic;
        }

        *[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .x-gmail-data-detectors,
        .x-gmail-data-detectors *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
        }

        .a6S {
	        display: none !important;
	        opacity: 0.01 !important;
        }

        img.g-img + div {
	        display:none !important;
	   	}

        .button-link {
            text-decoration: none !important;
        }

        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) { 
            .email-container {
                min-width: 375px !important;
            }
        }
        
        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }
        .button-td:hover,
        .button-a:hover {
            background: #555555 !important;
            border-color: #555555 !important;
        }
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: auto !important;
            }

            .fluid {
                max-width: 100% !important;
                height: auto !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            .stack-column,
            .stack-column-center {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }

            .stack-column-center {
                text-align: center !important;
            }

            .center-on-narrow {
                text-align: center !important;
                display: block !important;
                margin-left: auto !important;
                margin-right: auto !important;
                float: none !important;
            }
            table.center-on-narrow {
                display: inline-block !important;
            }

			.email-container p {
				font-size: 17px !important;
				line-height: 22px !important;
			}
			
        }
    </style>

</head>
<body width="100%" bgcolor="#222222" style="margin: 0; mso-line-height-rule: exactly;">
    <center style="width: 100%; background: #222222; text-align: left;">
        <table role="presentation" aria-hidden="true" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
			<tr>
				<td style="padding: 20px 0; text-align: center">
					
				</td>
			</tr>
        </table>
        <!-- Email Header : END -->

        <!-- Email Body : BEGIN -->
        <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">

            @yield('content')

            <!-- Clear Spacer : BEGIN -->
            <tr>
                <td height="40" style="font-size: 0; line-height: 0;">
                    &nbsp;
                </td>
            </tr>
            <!-- Clear Spacer : END -->

            <!-- 1 Column Text : BEGIN -->
            <tr>
                <td bgcolor="#ffffff">
                    <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" width="100%">
                    	<tr>
                            <td style="padding: 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; text-align: justify;">
                                <small>@lang('email.terms')</small> 
                            </td>
							</tr>
                    </table>
                </td>
            </tr>
            <!-- 1 Column Text : END -->

        </table>
        <!-- Email Body : END -->

        <!-- Email Footer : BEGIN -->
        <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
            <tr>
                <td style="padding: 40px 10px;width: 100%;font-size: 12px; font-family: sans-serif; line-height:18px; text-align: center; color: #888888;" class="x-gmail-data-detectors">
                    <br>
                    {{ config('app.name') }}<br>@lang('common.sentence')<br><a style="color:#888888" href="https://www.omarbarbosa.com">www.omarbarbosa.com</a>
                    <br><br>
                <unsubscribe><a style="color:#888888; text-decoration:underline;" href="{{ url('account/delete') }}" title="{{ trans('common.deleteAccount') }}">@lang('common.deleteAccount')</a></unsubscribe>
                </td>
            </tr>
        </table>
        <!-- Email Footer : END -->

    </center>
</body>
</html>
