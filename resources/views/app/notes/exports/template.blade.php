<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@lang('notes.title')</title>
    <style>
        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        b {
            font-weight: bolder;
        }

        small {
            font-size: 80%;
        }

        a {
            color: #007bff;
            text-decoration: none;
            background-color: transparent;
        }

        a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        small {
            font-size: 80%;
            font-weight: 400;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .col-12 {
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }

        .col-12 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .border-top {
            border-top: 1px solid #dee2e6!important;
        }

        .d-block {
            display: block!important;
        }

        .mt-2 {
            margin-top: 0.5rem!important;
        }

        .my-4 {
            margin-top: 1.5rem!important;
        }

        .my-4 {
            margin-bottom: 1.5rem!important;
        }

        .text-muted {
            color: #6c757d!important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        @foreach ($notes as $note)
            @include('app.notes.note')
        @endforeach
    </div>
</body>
</html>