<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>MagangIn - JTI Polinema</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
  <link
      rel="icon"
      href="img/M_Logo.png"
      type="image/x-icon"
    />
  <!-- ✅ Tambahkan CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Fonts and Icons -->
  <script src="assets/js/plugin/webfont/webfont.min.js"></script>
  <script>
    WebFont.load({
      google: { families: ["Public Sans:300,400,500,600,700"] },
      custom: {
        families: [
          "Font Awesome 5 Solid",
          "Font Awesome 5 Regular",
          "Font Awesome 5 Brands",
          "simple-line-icons"
        ],
        urls: ["assets/css/fonts.min.css"]
      },
      active: function () {
        sessionStorage.fonts = true;
      }
    });
  </script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- CSS Files -->
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/css/plugins.min.css" />
  <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
  <link rel="stylesheet" href="assets/css/demo.css" />
</head>
<body>
  <div class="wrapper sidebar_minimize">
    @include('layouts.sidebar')

    <div class="main-panel">
      @include('layouts.header')

      <div class="container">
        <div class="page-inner">
          @include('layouts.breadcrumb')

          <div class="form">
            <section class="content">
              @yield('content')
            </section>
          </div>
        </div>
      </div>

      @include('layouts.footer')
    </div>
  </div>

  <!-- Core JS Files -->
  <script src="assets/js/core/jquery-3.7.1.min.js"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>

  <!-- Plugins -->
  <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
  <script src="assets/js/plugin/chart.js/chart.min.js"></script>
  <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>
  <script src="assets/js/plugin/chart-circle/circles.min.js"></script>
  <script src="assets/js/plugin/datatables/datatables.min.js"></script>
  {{-- <script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script> --}}
  <script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
  <script src="assets/js/plugin/jsvectormap/world.js"></script>
  <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>
  <script src="assets/js/kaiadmin.min.js"></script>
  <script src="assets/js/setting-demo.js"></script>
  <script src="assets/js/demo.js"></script>


  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
  <!-- Plugin Validasi Tambahan -->
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- CDN JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <!-- Optional Charts -->
  <script>
    $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
      type: "line", height: "70", width: "100%", lineWidth: "2",
      lineColor: "#177dff", fillColor: "rgba(23, 125, 255, 0.14)"
    });
  </script>

  <!-- ✅ Tambahkan ini agar halaman bisa inject script -->
  @stack('js')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Modal Global -->
  <div class="modal fade" id="globalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="modal-content">
            <!-- Konten modal akan diisi dari AJAX -->
        </div>
    </div>
  </div>
</body>
</html>