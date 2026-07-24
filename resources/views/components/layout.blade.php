<!--
=========================================================
* Material Dashboard 2 - v3.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
@props(['bodyClass'])
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/sima-mark.svg') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/sima-mark.svg') }}">
    <title>SIMA | Sistem Informasi Manajemen Aset</title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets') }}/css/nucleo-icons.css" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/2afeabd839.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    @vite('resources/js/app.js')
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets') }}/css/material-dashboard.css" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/asset-management.css" rel="stylesheet" />
</head>
<body class="{{ $bodyClass }}">

<div class="sima-page-loader" id="simaPageLoader" aria-live="polite" aria-label="Memuat halaman">
    <img src="{{ asset('img/sima-mark.svg') }}" alt="" aria-hidden="true">
    <span class="sima-spinner"></span>
    <small>Menyiapkan SIMA...</small>
</div>

{{ $slot }}

<script src="{{ asset('assets') }}/js/core/popper.min.js"></script>
<script src="{{ asset('assets') }}/js/core/bootstrap.min.js"></script>
<script src="{{ asset('assets') }}/js/plugins/perfect-scrollbar.min.js"></script>
<script src="{{ asset('assets') }}/js/plugins/smooth-scrollbar.min.js"></script>
@stack('js')
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

</script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="{{ asset('assets') }}/js/material-dashboard.min.js?v=3.0.0"></script>
<script>
    window.addEventListener('load', function () {
        document.getElementById('simaPageLoader')?.classList.add('is-hidden');
    });
    document.addEventListener('submit', function (event) {
        if (!event.defaultPrevented && event.target.checkValidity()) {
            document.getElementById('simaPageLoader')?.classList.remove('is-hidden');
        }
    });
</script>
</body>
</html>
