<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>MagangIn - JTI Polinema</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
  <link rel="icon" href="{{ asset('img/MagangIn.png') }}" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
   <link
      rel="icon"
      href="img/M_Logo.png"
      type="image/x-icon"
    />
  <style>
    .toast-error {
    background-color: #ff4444 !important;
    color: #ffffff !important;
}
  </style>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Fonts and Icons -->
  <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
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
        urls: ["{{ asset('assets/css/fonts.min.css') }}"]
      },
      active: function () {
        sessionStorage.fonts = true;
      }
    });
  </script>
  

  <!-- Core CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

  <!-- DataTables CSS (if you need it) -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

  @stack('css')
</head>

<body>
  <div class="wrapper">
    @include('layouts.sidebar_mahasiswa')

    <div class="main-panel">
      @include('layouts.header')

      <div class="container">
        <div class="page-inner">
          @include('layouts.breadcrumb')

          <section class="content">
            @yield('content')
          </section>
        </div>
      </div>

      @include('layouts.footer')
    </div>
  </div>

  <!-- Core JS Files -->
  <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

  <!-- Plugins -->
  <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
  {{-- <script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script> --}}
  <script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
  <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>
  <script src="{{ asset('assets/js/setting-demo.js') }}"></script>
  <script src="{{ asset('assets/js/demo.js') }}"></script>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
  <!-- Plugin Validasi Tambahan -->
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- CDN JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <!-- DataTables (if you need it) -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <!-- 
    NOTE: We have REMOVED demo.js and jsvectormap scripts from the layout,
    because your “rekomendasi” pages do not contain any <canvas> or vector‐map containers.
    If you need them on other pages, load them only via @push('js') in those specific pages.
  -->

  @push('js')
  <script>
    // waits for jQuery to be ready, then wires up the buttons
    $(function(){
      $('.toggle-sidebar, .sidenav-toggler').on('click', function(e){
        e.preventDefault();
        $('.wrapper').toggleClass('sidebar_minimize');
      });
    });
  </script>
@endpush

  @stack('js')

  <!-- Modal Global (you probably have some modal that gets injected via AJAX onto main layout) -->
  <div class="modal fade" id="globalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content" id="modal-content">
        {{-- Konten modal akan diisi dari AJAX --}}
      </div>
    </div>
  </div>
</body>

</html>



