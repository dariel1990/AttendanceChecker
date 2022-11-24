@extends('layouts.app')
@push('page-css')
    <link href="{{ asset('/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
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

        .swal-text {
            text-align: center;
        }

        .swal-modal {
            width: 400px;
        }

        .swal-footer {
            text-align: center;
        }

    </style>
@endpush
@section('content')

        <!-- Start Content-->
        <div class="container">
            <div class="row mt-4">
                <div class="col-9">
                    <div class="card">
                        <div class="card-body">
                            @if(Session::has('success'))
                                <p class="alert alert-success">{{ Session::get('success') }}</p>
                            @endif
                            <table class="table table-nowrap table-sm table-hover" width="100%" id="request-table">
                                <thead>
                                    <tr>
                                        <th class="fw-bold">Employee Name</th>
                                        <th class="text-center fw-bold">Date Requested</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="imagePreview"></div>
                </div>
            </div>
            <!-- end row -->
        </div>
@endsection
@push('page-scripts')
    <script src="{{ asset ('/assets/js/calendar1.js') }}"></script>
    <script src="{{ asset('/assets/libs/winbox/winbox.bundle.js') }}"></script>
    <script src="{{ asset('/assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('/assets/libs/sweetalert2/sweetalert.min.js') }}"></script>
    <script src="{{ asset('/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        let imageRequests = $('#request-table').DataTable({
            serverSide: true,
            processing: true,
            destroy : true,
            ordering: false,
            info: false,
            paging: false,
            language: {
                processing: '<i class="text-primary fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>',
            },
            ajax : `/imageRequests/lists`,
            columns : [
                {
                    class : 'lead align-middle fw-medium',
                    data : 'employee',
                    name : 'employee',
                    searchable: true,
                    orderable: false
                },
                {
                    class : 'lead align-middle fw-medium text-center',
                    data : 'date_requested',
                    name : 'date_requested',
                    searchable: false,
                    orderable: false
                }
            ],
            "createdRow": function( row, data, dataIndex ) {
                $(row).children().css('cursor', 'pointer');
                $(row).children().addClass('preview');
                $(row).children().attr('data-id', data.id);
                $(row).children().attr('data-key', data.Employee_id);
                $(row).children().attr('id-image', data.IdPhoto);
                $(row).children().attr('sig-image', data.SignaturePhoto);
            }
        });

        $(document).on('click', '.preview', function () {
            let requestID = $(this).attr('data-id');
            let employeeID = $(this).attr('data-key');
            let idImage = $(this).attr('id-image');
            let sigImage = $(this).attr('sig-image');

            $('tr').children().removeClass('table-primary text-primary');
            $(this).parent().children().addClass('table-primary text-primary');
            if(sigImage == 'null'){
                $('.imagePreview')
                .html('')
                .append(`
                <img class="img-thumbnail shadow mb-1" width="100%" src="/assets/ImageRequests/${idImage}">
                <input type="hidden" name="Employee_id" value="${employeeID}">
                <div class="d-grid">
                    <button type="button" class="btn btn-primary mb-1" onClick="submitDetailsForm(${requestID})" >Accept Request</button>
                    <button type="button" class="btn btn-danger" onClick="deleteRequest(${requestID})" >Delete Request</button>
                </div>`);
            }else if(idImage == 'null'){
                $('.imagePreview')
                .html('')
                .append(`
                <img class="img-thumbnail shadow mb-2" width="100%" src="/assets/ImageRequests/${sigImage}">
                <input type="hidden" name="Employee_id" value="${employeeID}">
                <div class="d-grid">
                    <button type="button" class="btn btn-primary mb-1" onClick="submitDetailsForm(${requestID})" >Accept Request</button>
                    <button type="button" class="btn btn-danger" onClick="deleteRequest(${requestID})" >Delete Request</button>
                </div>`);
            }else{
                $('.imagePreview')
                .html('')
                .append(`
                <img class="img-thumbnail shadow mb-1" width="100%" src="/assets/ImageRequests/${idImage}">
                <img class="img-thumbnail shadow mb-2" width="100%" src="/assets/ImageRequests/${sigImage}">
                <input type="hidden" name="Employee_id" value="${employeeID}">
                <div class="d-grid">
                    <button type="button" class="btn btn-primary mb-1" onClick="submitDetailsForm(${requestID})" >Accept Request</button>
                    <button type="button" class="btn btn-danger" onClick="deleteRequest(${requestID})" >Delete Request</button>
                </div>`);
            }

        });

        function submitDetailsForm(id) {
            console.log(id);
            swal({
                text: "Are you sure you want to accept this?",
                icon: "warning",
                buttons: [
                    'No',
                    'Yes!'
                ],
                dangerMode: true,
                closeOnClickOutside: false,
            }).then((willAccept) => {
                if (willAccept) {
                    $.ajax({
                        url : `/acceptImageRequest/${id}`,
                        method : 'POST',
                        success : function (response) {
                            swal({
                                text : 'Changes accepted. You can now print his/her ID.',
                                icon : 'success',
                                timer : 1500,
                                buttons : false,
                            });
                            imageRequests.ajax.reload();
                            $('.imagePreview').html('');
                        },
                    });
                }
            });

        }

        function deleteRequest(id) {
            swal({
                text: "Are you sure you want to delete this?",
                icon: "warning",
                buttons: [
                    'No',
                    'Yes!'
                ],
                dangerMode: true,
                closeOnClickOutside: false,
            }).then((willAccept) => {
                if (willAccept) {
                    $.ajax({
                        url : `/deleteRequest/${id}`,
                        method : 'DELETE',
                        success : function (response) {
                            swal({
                                text : 'Successfully deleted!',
                                icon : 'success',
                                timer : 1500,
                                buttons : false,
                            });
                            imageRequests.ajax.reload();
                            $('.imagePreview').html('');
                        },
                    });
                }
            });

        }


    </script>
@endpush
