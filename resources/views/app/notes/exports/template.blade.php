<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@lang('notes.title')</title>
    <style>
            .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 1rem;
            }

            .table th,
            .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #eceeef;
            }

            .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #eceeef;
            }

            .table tbody + tbody {
            border-top: 2px solid #eceeef;
            }

            .table .table {
            background-color: #fff;
            }

            .table-sm th,
            .table-sm td {
            padding: 0.3rem;
            }

            .table-bordered {
            border: 1px solid #eceeef;
            }

            .table-bordered th,
            .table-bordered td {
            border: 1px solid #eceeef;
            }

            .table-bordered thead th,
            .table-bordered thead td {
            border-bottom-width: 2px;
            }

            .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
            }

            .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
            }

            .table-active,
            .table-active > th,
            .table-active > td {
            background-color: rgba(0, 0, 0, 0.075);
            }

            .table-hover .table-active:hover {
            background-color: rgba(0, 0, 0, 0.075);
            }

            .table-hover .table-active:hover > td,
            .table-hover .table-active:hover > th {
            background-color: rgba(0, 0, 0, 0.075);
            }

            .table-success,
            .table-success > th,
            .table-success > td {
            background-color: #dff0d8;
            }

            .table-hover .table-success:hover {
            background-color: #d0e9c6;
            }

            .table-hover .table-success:hover > td,
            .table-hover .table-success:hover > th {
            background-color: #d0e9c6;
            }

            .table-info,
            .table-info > th,
            .table-info > td {
            background-color: #d9edf7;
            }

            .table-hover .table-info:hover {
            background-color: #c4e3f3;
            }

            .table-hover .table-info:hover > td,
            .table-hover .table-info:hover > th {
            background-color: #c4e3f3;
            }

            .table-warning,
            .table-warning > th,
            .table-warning > td {
            background-color: #fcf8e3;
            }

            .table-hover .table-warning:hover {
            background-color: #faf2cc;
            }

            .table-hover .table-warning:hover > td,
            .table-hover .table-warning:hover > th {
            background-color: #faf2cc;
            }

            .table-danger,
            .table-danger > th,
            .table-danger > td {
            background-color: #f2dede;
            }

            .table-hover .table-danger:hover {
            background-color: #ebcccc;
            }

            .table-hover .table-danger:hover > td,
            .table-hover .table-danger:hover > th {
            background-color: #ebcccc;
            }

            .thead-inverse th {
            color: #fff;
            background-color: #292b2c;
            }

            .thead-default th {
            color: #464a4c;
            background-color: #eceeef;
            }

            .table-inverse {
            color: #fff;
            background-color: #292b2c;
            }

            .table-inverse th,
            .table-inverse td,
            .table-inverse thead th {
            border-color: #fff;
            }

            .table-inverse.table-bordered {
            border: 0;
            }

            .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -ms-overflow-style: -ms-autohiding-scrollbar;
            }

            .table-responsive.table-bordered {
            border: 0;
            }

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
    <table class="table">
        <thead>
            <tr>
                <th scope="col">
                    @lang('common.date')
                </th>
                <th scope="col">
                    @lang('notes.author')
                </th>
                <th scope="col" colspan="2">
                    @lang('notes.title')
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($notes as $note)
                <tr>
                    <th scope="row">{{ $note->created_at }}</th>
                    <td>{{ $note->team_member_name }}</td>
                    <td>{!! $note->content !!}</td>
                </tr>
                <tr>
                    <td colspan="4">
                        @lang('notes.tags'): {{ $note->tags->implode('slug',', ') }}
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
</body>
</html>
