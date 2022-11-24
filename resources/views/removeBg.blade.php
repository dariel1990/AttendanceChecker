@extends('layouts.app')
@push('page-css')
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .winbox {
            background: linear-gradient(90deg, #ff00f0, #0050ff);
            border-radius: 5px 5px;
        }
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .select2-container--default .select2-selection--single{
            padding:6px;
            height: 40px;
            font-size: 1.2em;
            position: relative;
        }

    </style>
@endpush
@section('content')


@endsection
@push('page-scripts')
    <script src="{{ asset ('assets/js/calendar1.js') }}"></script>
    <script src="{{ asset('/assets/libs/winbox/winbox.bundle.js') }}"></script>
    <script src="{{ asset('/assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('/assets/libs/sweetalert2/sweetalert.min.js') }}"></script>
    <script>
        const settings = {
            "async": true,
            "crossDomain": true,
            "url": "https://background-removal.p.rapidapi.com/remove",
            "method": "POST",
            "headers": {
                "content-type": "application/x-www-form-urlencoded",
                "X-RapidAPI-Key": "bc8d515256mshd9e8b8fdb2cd02cp1b4bccjsn49f2f6f7a637",
                "X-RapidAPI-Host": "background-removal.p.rapidapi.com"
            },
            "data": {
                "image_url": "https://objectcut.com/assets/img/raven.jpg"
            }
        };

        $.ajax(settings).done(function (response) {
            console.log(response);
        });
    </script>
@endpush
