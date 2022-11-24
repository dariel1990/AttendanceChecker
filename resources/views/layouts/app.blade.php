<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @stack('meta-data')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title id="parentLayoutPageTitle">@yield('page-title'){{ $pageTitle }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to bui    ld CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- third party css -->
    <link href="{{ asset ('assets/css/vendor/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- third party css end -->

    <!-- App css -->
    <link href="{{ asset ('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset ('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="light-style" />
    <link href="{{ asset ('assets/css/app-dark.min.css') }}" rel="stylesheet" type="text/css" id="dark-style" />
    <!-- SweedAlert -->
    <link href="{{ asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    @stack('page-css')
</head>
<body class="loading" data-layout="topnav" data-layout-config='{"layoutBoxed":false,"darkMode":false,"showRightSidebarOnStart": false}'>
        <!-- Begin page -->
        <div class="wrapper">

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">
                    <!-- Topbar Start -->
                    <div class="navbar-custom topnav-navbar">
                        <div class="container-fluid">

                            <!-- LOGO -->
                            <a href="#" class="topnav-logo">
                                <span class="topnav-logo-lg">
                                    <img src="assets/images/id-logo.png" alt="" height="50">
                                </span>
                                <span class="topnav-logo-sm">
                                    <img src="assets/images/id-logo.png" alt="" height="50">
                                </span>
                            </a>
                        </div>
                    </div>
                    <!-- end Topbar -->

                    <div class="topnav">
                        <div class="container-fluid">
                            <nav class="navbar navbar-dark navbar-expand-lg topnav-menu">

                                <div class="collapse navbar-collapse" id="topnav-menu-content">
                                    <ul class="navbar-nav">
                                        <a class="nav-link dropdown-toggle arrow-none h1 " href="/">
                                            <i class="mdi mdi-card-account-details-outline me-1"></i> Attendees Registration
                                        </a>
                                        <a class="nav-link dropdown-toggle arrow-none h1 " href="/attendanceList">
                                            <i class="mdi mdi-card-account-details-outline me-1"></i> Attendance List
                                        </a>
                                    </ul>
                                </div>
                            </nav>
                        </div>
                    </div>
                    <!-- end Topbar -->
                    @yield('content')
                </div>
            </div>
                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <script>document.write(new Date().getFullYear())</script> © Information Technology Unit
                            </div>
                            <div class="col-md-6">
                                <div class="text-md-end footer-links d-none d-md-block">
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->
            </div>
        </div>

    </div>
    <!-- bundle -->

    <script src="{{ asset ('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset ('assets/js/app.min.js') }}"></script>
    {{-- <script src="{{ asset ('assets/js/vendor/fullcalendar.min.js') }}"></script> --}}
    <script src="{{ asset('/assets/libs/jquery/jquery.min.js') }}"></script>
    <!-- SweetAlert -->
    <script src="{{ asset('/assets/libs/sweetalert2/sweetalert.min.js') }}"></script>
    <script>
        $(function () {
            if(screen.width == '1366' && screen.height == '768'){
                document.body.style.zoom = '80%';
            }
        });
    </script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @stack('page-scripts')
</body>

</html>
