<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ Config::get('app.name') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- toastr -->
    <link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/css/alt/adminlte.light.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">

    <!-- jQuery -->
    <script src="/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="/plugins/select2/js/select2.full.min.js"></script>
    <!-- InputMask -->
    <script src="/plugins/moment/moment.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- EJS -->
    <script src="/plugins/ejs/ejs.min.js"></script>
    <!-- jQuery -->
    <script src="/plugins/toastr/toastr.min.js"></script>
    <script>
        toastr.options.closeButton = true;
        toastr.options.progressBar = true;
        toastr.options.positionClass = 'toast-top-center';
        toastr.options.newestOnTop = true;
    </script>
    <!-- AdminLTE App -->
    <script src="/js/adminlte.min.js"></script>
    <!-- Form -->
    <script src="/js/form.js"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <x-preloader />
        <x-navbar />
        <x-sidebar />

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper pb-3">
            {!! $slot !!}
        </div>

        <x-footer />
    </div>
    <!-- ./wrapper -->

    <x-modal-delete />
</body>

</html>
