@extends('layouts.app')
@push('page-css')
    <meta name="viewport" content="width=device-width, user-scalable=no">
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

        <!-- Start Content-->
        <div class="container-fluid">
            <div class="row mt-4">
                <div class="col-2"></div>
                <div class="col-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-7 p-4">
                                    <label for="employeeName" class="mb-1 font-20">SELECT EMPLOYEE</label>
                                    <select class="form form-select form-select-lg" name="employeeName" id="employeeName">
                                        <option value="">Search name here</option>
                                        @foreach($employees as $employee)
                                            <option
                                                data-employeeID="{{ $employee->Employee_id }}"
                                                data-fullname="{{ $employee->fullname }}"
                                                data-office="{{ $employee->office_assignment->Description }}"
                                                data-position="{{ $employee->position->Description }}"
                                                value="{{ $employee->Employee_id }}">{{ $employee->LastName }},
                                                {{ $employee->FirstName }} {{ $employee->MiddleName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <h4 class="mt-3 h2">Employee Details</h4><hr class="m-0 mb-2">
                                    <div class="row mb-1 mt-1">
                                        <div class="col-12">
                                            <div class="input-group">
                                                <span class="input-group-text font-20" style="width:150px">FULL NAME</span>
                                                <input id="fullname" type="text" class="form-control font-20">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-12">
                                            <div class="input-group">
                                                <span class="input-group-text font-20" style="width:150px">OFFICE</span>
                                                <input id="office" type="text" class="form-control font-20">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-12">
                                            <div class="input-group">
                                                <span class="input-group-text font-20" style="width:150px">POSITION</span>
                                                <input id="position" type="text" class="form-control font-20">
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="m-0">
                                    <div class="d-grid mt-2">
                                        <button type="button" class="btn btn-primary generateCard mb-1 font-20">PRINT RAFFLE TICKET</button>
                                    </div>
                                </div>
                                <div class="col-5 p-4">
                                    <div class="imagePreview">
                                        <img width="100%" src="{{ asset('assets/images/sds-logo.png') }}" class="mb-3 img-fluid img-thumbnail mx-auto d-block shadow">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-2"></div>
            </div>
            <!-- end row -->

        </div>
@endsection
@push('page-scripts')
    <script src="{{ asset ('assets/js/calendar1.js') }}"></script>
    <script src="{{ asset('/assets/libs/winbox/winbox.bundle.js') }}"></script>
    <script src="{{ asset('/assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('/assets/libs/sweetalert2/sweetalert.min.js') }}"></script>
    <script>

        $("#employeeName").select2();
        $('b[role="presentation"]').hide();
        $('#employeeName').change(function(e) {
            let [selectedItem] = $("#employeeName option:selected");
            let employeeID = selectedItem.getAttribute('data-employeeID') || '';
            let fullname = selectedItem.getAttribute('data-fullname') || '';
			let position = selectedItem.getAttribute('data-position') || '';
			let office = selectedItem.getAttribute('data-office') || '';

            $('.generateCard').attr('data-id', employeeID);
            $('.generateBigCard').attr('data-id', employeeID);
			$('.exportImage').attr('data-id', employeeID);
            $('.exportSignature').attr('data-id', employeeID);

			$('#position').val(position);
			$('#office').val(office);
            $('#fullname').val(fullname);

            $.ajax({
                url : `/generateIdPicture/${employeeID}`,
                success : function (record) {
                    $('.imagePreview')
                        .html('')
                        .append(`
                        <img class="img-thumbnail shadow mb-1" width="100%" src="/assets/GeneratedImages/${employeeID}.jpg">`);
                },
            });

        });

        $(document).on('click', '.generateCard', function () {
            let employeeID = $("#employeeName").val();
            let id = $(this).attr('data-id');
            let IDNoFontSize        = $('#IDNoFontSize').val();
            let nameFontSize        = $('#nameFontSize').val();
            let positionFontSize    = $('#positionFontSize').val();
            let addressFontSize     = $('#addressFontSize').val();
            let officeFontSize      = $('#officeFontSize').val();
            let pictureSize         = $('#pictureSize').val();
			let position      		= $('#position').val();
            let office         		= $('#office').val();

            if(employeeID == ''){
                swal({
                    text : 'Select employee first!',
                    icon : 'warning',
                    timer : 1500,
                    buttons : false,
                });
            }else{
                $('#employeeName').prop('disabled', true);
                $('.generateCard').attr('disabled', true);
                $('.generateBigCard').attr('disabled', true);
                $('.exportImage').attr('disabled', true);
                $('.exportSignature').attr('disabled', true);

                let ROUTE = `production/${id}/${IDNoFontSize}/${nameFontSize}/${positionFontSize}/${addressFontSize}/${officeFontSize}/${pictureSize}/${position}/${office}`;
                let screen_width = 0;
                let screen_height = 0
                let screen_x = 0;

                if(screen.width == '1366' && screen.height == '768'){
                    screen_width = window.innerWidth - (window.innerWidth * .12);
                    screen_height = window.innerHeight - (window.innerHeight * .30);
                    screen_x = (screen_width * .40);
                }else if(screen.width == '1920' && screen.height == '1080'){
                    screen_width = window.innerWidth - (window.innerWidth * .31);
                    screen_height =window.innerHeight - (window.innerHeight * .25);
                    screen_x = (window.innerWidth * .30);
                }

                generate = new WinBox(`ID PRODUCTION`, {
                    root: document.querySelector('.page-content'),
                    class: ["no-min", "no-full", "no-resize", "no-move", "no-shadow"],
                    title : "Deductions",
                    url: ROUTE,
                    index: 999999,
                    background: "#2a3042",
                    border: 4,
                    width: screen_width,
                    height: screen_height,
                    x: screen_x,
                    y: 280,
                    onclose: function(force){
                        $('#employeeName').prop('disabled', false);
                        $('.generateCard').attr('disabled', false);
                        $('.generateBigCard').attr('disabled', false);
                        $('.exportImage').attr('disabled', false);
                        $('.exportSignature').attr('disabled', false);
                    }
                })

            }
        });

    </script>
@endpush
