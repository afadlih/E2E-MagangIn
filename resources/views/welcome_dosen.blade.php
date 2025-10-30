@extends('layouts.template_dsn')

@section('content')

<!-- Modal (dipakai untuk load data dinamis via AJAX, tidak memuat dashboard) -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <!-- Konten modal akan dimuat dengan JS -->
    </div>
  </div>
</div>
<div class="card">
          <!-- ========= 1. Hero Banner ========== -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-4 position-relative overflow-hidden">
                    <div class="hero-bg position-absolute" style="inset:0;
                        background: radial-gradient(circle at 0% 50%, rgba(99,102,241,.25) 0%, rgba(99,102,241,0) 60%),
                                    linear-gradient(90deg, rgba(99,102,241,.15) 0%, rgba(255,255,255,0) 70%);">
                    </div>
                    <h3 class="fw-bold mb-1 position-relative">
                        Senang Bertemu Anda Kembali <span class="text-primary">{{ Auth::user()->name }}</span> 👋
                    </h3>
                    <p class="mb-0 text-secondary position-relative">
                    </p>
                </div>
            </div>
        </div>

<!-- Dashboard Content -->
<div class="row">
  <!-- Total Mahasiswa -->
  <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-icon">
            <div class="icon-big text-center icon-primary bubble-shadow-small">
              <i class="fas fa-users"></i>
            </div>
          </div>
          <div class="col col-stats ms-3 ms-sm-0">
            <div class="numbers">
              <p class="card-category">Total Mahasiswa JTI</p>
              <h4 class="card-title">{{ $totalMhs }}</h4> 
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Mahasiswa Diterima -->
  <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-icon">
            <div class="icon-big text-center icon-info bubble-shadow-small">
              <i class="fas fa-user-check"></i>
            </div>
          </div>
          <div class="col col-stats ms-3 ms-sm-0">
            <div class="numbers">
              <p class="card-category">Mahasiswa Diterima Magang</p>
              <h4 class="card-title">{{ $totalMhsDiterima }}</h4> 
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Perusahaan -->
  <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-icon">
            <div class="icon-big text-center icon-success bubble-shadow-small">
             <i class="fas fa-building"></i>
            </div>
          </div>
          <div class="col col-stats ms-3 ms-sm-0">
            <div class="numbers">
              <p class="card-category">Perusahaan Mitra</p>
              <h4 class="card-title">{{ $totalPerusahaan }}</h4> 
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Lowongan -->
  <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-icon">
            <div class="icon-big text-center icon-secondary bubble-shadow-small">
             <i class="fas fa-briefcase"></i>
            </div>
          </div>
          <div class="col col-stats ms-3 ms-sm-0">
            <div class="numbers">
              <p class="card-category">Lowongan Tersedia</p>
              <h4 class="card-title">{{ $totalLowongan }}</h4> 
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Perbandingan Total Mahasiswa dengan Mahasiswa Diterima Magang</h4>
      </div>
      <div class="card-body">
        <canvas id="chartMahasiswa"></canvas>
      </div>
    </div>
  </div>
</div>

@endsection

@push('js')
<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
window.addEventListener('DOMContentLoaded', function () {
  const ctx = document.getElementById('chartMahasiswa').getContext('2d');

  const chartMahasiswa = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Total Mahasiswa', 'Diterima Magang'],
      datasets: [{
        label: 'Jumlah',
        data: [{{ $totalMhs }}, {{ $totalMhsDiterima }}],
        backgroundColor: 'rgba(54, 162, 235, 0.4)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 2,
        tension: 0.4,
        fill: true,
        pointRadius: 5,
        pointBackgroundColor: 'rgba(54, 162, 235, 1)',
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0
          }
        }
      }
    }
  });
});
</script>

<script>
  $(function () {
    // Setup CSRF token untuk semua AJAX
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
  });

  function modalAction(url = '') {
    $('#myModal .modal-content').load(url, function () {
      $('#myModal').modal('show');
    });
  }
</script>
@endpush
