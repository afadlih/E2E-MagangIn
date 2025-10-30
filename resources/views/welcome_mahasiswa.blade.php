{{-- resources/views/welcome_mahasiswa.blade.php --}}

@extends('layouts.template_mhs')

@push('css')
<style>
  /* ===== Root Theme Colors ===== */
  :root {
    --primary: #6366f1; /* Indigo 500 */
    --secondary: #64748b; /* Slate 500 */
    --success: #10b981; /* Emerald 500 */
    --warning: #f59e0b; /* Amber 500 */
    --danger:  #ef4444; /* Red 500 */
    --bg-light: #f6f8fc;
  }

/* test */

  /* ===== Base ===== */
  body {
    background: var(--bg-light);
  }

  /* ===== Utility ===== */
  .card-stat {
    transition: transform .25s ease, box-shadow .25s ease;
  }
  .card-stat:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 10px 24px rgba(0,0,0,.08);
  }
  .fade-slide {
    opacity: 0;
    transform: translateY(24px);
  }
  .fade-slide.show {
    opacity: 1;
    transform: translateY(0);
    transition: opacity .8s ease-out, transform .8s ease-out;
  }

  /* ===== Badges ===== */
  .deadline-badge {
    background: var(--danger);
    color: #fff;
    font-size: .75rem;
    padding: .35rem .55rem;
    font-weight: 600;
    border-radius: .35rem;
  }

  /* ===== Hero Banner Background ===== */
  .hero-bg {
    z-index: 0;
    border-radius: inherit;
  }
  .hero-banner > * {
    position: relative;
    z-index: 1;
  }


    /* Stepper Container */
  .stepper {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  /* Each Step Box */
  .stepper-item {
    position: relative;
    text-align: center;
    flex: 1;
  }

  /* The Circle for Each Step */
  .stepper-circle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background-color: var(--primary);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform .3s ease, box-shadow .3s ease;
    font-weight: 600;
    font-size: 1.1rem;
  }

  /* Hover State on the Circle */
  .stepper-item:hover .stepper-circle {
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
  }

  /* Step Label */
  .stepper-label {
    font-size: 0.9rem;
    color: var(--secondary);
  }

  /* Arrow Icon Between Steps */
  .stepper-arrow {
    position: absolute;
    top: 50%;
    right: -16px;
    transform: translateY(-50%);
    color: var(--secondary);
    font-size: 1.2rem;
  }

  /* Hide the arrow on the last step */
  .stepper-item:last-child .stepper-arrow {
    display: none;
  }


    /* Container Card */
  .info-card {
    background: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    overflow: hidden;
    position: relative;
  }

  /* Colored header bar */
  .info-card-header {
    background: linear-gradient(90deg, rgba(99,102,241,0.9) 0%, rgba(99,102,241,0.6) 100%);
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
  }
  .info-card-header i {
    font-size: 1.5rem;
    color: #ffffff;
    margin-right: 0.75rem;
  }
  .info-card-header h5 {
    margin: 0;
    color: #ffffff;
    font-weight: 600;
    font-size: 1.25rem;
  }

  /* Body with left accent border */
  .info-card-body {
    border-left: 4px solid #6366f1; /* Indigo 500 */
    padding: 1.5rem;
  }

  /* Paragraph text */
  .info-card-body p {
    color: #374151; /* Slate 700 */
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 1rem;
  }

  /* Custom bullets */
  .info-list {
    list-style: none;
    padding-left: 0;
    margin-bottom: 0;
  }
  .info-list li {
    display: flex;
    align-items: flex-start;
    margin-bottom: 0.75rem;
  }
  .info-list li i {
    font-size: 1rem;
    margin-right: 0.5rem;
    color: #6366f1; /* Indigo 500 */
    margin-top: 0.15rem;
  }
  .info-list li span {
    color: #4B5563; /* Gray-700 */
    font-size: 0.95rem;
    line-height: 1.5;
  }

  /* Hover effect—slight lift on entire card */
  .info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

    /* Make Upcoming Deadline rows clickable */
  #upcoming-list .detail-link {
    cursor: pointer;
    transition: background-color 0.2s ease;
  }
  #upcoming-list .detail-link:hover {
    background-color: #f1f5f9;
  }

</style>
@endpush

@section('content')
<div class="card">
  <div class="card-header">
    <div class="d-flex gap-2 align-items-center flex-wrap">
    
    </div>
  </div>

  <div class="card-body">
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"></div>
      </div>
    </div>
<div class="container-fluid mt-5 fade-slide">
  <!-- ========= 1. Hero ========== -->
  <div class="row mb-5">
    <div class="col-12">
      <div class="bg-white rounded-4 shadow-sm p-4 position-relative overflow-hidden">
        <div class="hero-bg position-absolute"
             style="inset:0;
                    background: radial-gradient(circle at 0% 50%, rgba(99,102,241,.25) 0%, rgba(99,102,241,0) 60%),
                                linear-gradient(90deg, rgba(99,102,241,.15) 0%, rgba(255,255,255,0) 70%);">
        </div>
        <h3 class="fw-bold mb-1 position-relative">
          Selamat Datang, <span class="text-primary">{{ Auth::user()->name }}</span> 👋
        </h3>
        <p class="mb-0 text-secondary position-relative">
          Ringkasan aktivitas &amp; rekomendasi magang Anda.
        </p>
      </div>
    </div>
  </div>

  <!-- ========= 1.1 How It Works (Stepper) ========= -->
<div class="row mb-5">
  <div class="col-12">
    <div class="bg-white rounded-4 shadow-sm p-4">
      <h4 class="fw-semibold mb-3">Cara Kerja Website Magang</h4>

      <div class="stepper">
        {{-- Step 1 --}}
        <div class="stepper-item">
          <div class="stepper-circle">1</div>
          <div class="stepper-label">Cari Lowongan</div>
          <i class="fas fa-chevron-right stepper-arrow"></i>
        </div>

        {{-- Step 2 --}}
        <div class="stepper-item">
          <div class="stepper-circle">2</div>
          <div class="stepper-label">Ajukan Lamaran</div>
          <i class="fas fa-chevron-right stepper-arrow"></i>
        </div>

        {{-- Step 3 --}}
        <div class="stepper-item">
          <div class="stepper-circle">3</div>
          <div class="stepper-label">Proses Seleksi</div>
          <i class="fas fa-chevron-right stepper-arrow"></i>
        </div>

        {{-- Step 4 --}}
        <div class="stepper-item">
          <div class="stepper-circle">4</div>
          <div class="stepper-label">Mulai Magang</div>
          {{-- no arrow on last step --}}
        </div>
      </div>

    </div>
  </div>
</div>

  <!-- ========= 1.5 Welcome Overview ========= -->
<div class="row mb-4">
  <div class="col-12">
    <div class="info-card">
      {{-- 1. Colored header with icon + title --}}
      <div class="info-card-header">
        <i class="fas fa-clipboard-check"></i>
        <h5>Tentang Portal Magang</h5>
      </div>

      {{-- 2. Body with border-left accent and custom bullets --}}
      <div class="info-card-body">
        <p>
          Portal ini membantu mahasiswa mencari dan mengajukan magang di berbagai perusahaan mitra. Anda dapat:
        </p>
        <ul class="info-list">
          <li>
            <i class="fas fa-search"></i>
            <span>Mencari lowongan berdasarkan <strong>posisi</strong>, <strong>lokasi</strong>, dan <strong>skill</strong></span>
          </li>
          <li>
            <i class="fas fa-clock"></i>
            <span>Melihat status lamaran secara <strong>real time</strong></span>
          </li>
          <li>
            <i class="fas fa-calendar-alt"></i>
            <span>Mengecek <strong>deadline</strong> terdekat agar tidak terlewat</span>
          </li>
          <li>
            <i class="fas fa-chart-line"></i>
            <span>Lihat <strong>rekomendasi otomatis</strong> berdasarkan profil Anda</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

  <!-- ========= 2. Metrics ========= -->
  <div class="row g-4 mb-5">
    <!-- Total Recommendations -->
    <div class="col-12 col-md-4">
      <div class="card card-stat shadow-sm border-0 h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <span class="fs-2 text-primary"><i class="fas fa-lightbulb"></i></span>
          <div>
            <span class="small text-secondary d-block">Total Recommendations</span>
            <h2 class="fw-semibold mb-0 counter" data-count="{{ $totalRecommendations }}">0</h2>
          </div>
        </div>
      </div>
    </div>
  <!-- In Progress Applications -->
  <div class="col-12 col-md-4">
    <div class="card card-stat shadow-sm border-0 h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <span class="fs-2 text-warning"><i class="fas fa-spinner"></i></span>
        <div>
          <span class="small text-secondary d-block">In Progress</span>
          <h2 class="fw-semibold mb-0 counter" data-count="{{ $inProgressApplications }}">0</h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Completed Applications -->
  <div class="col-12 col-md-4">
    <div class="card card-stat shadow-sm border-0 h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <span class="fs-2 text-success"><i class="fas fa-clipboard-list"></i></span>
        <div>
          <span class="small text-secondary d-block">Completed</span>
          <h2 class="fw-semibold mb-0 counter" data-count="{{ $completedApplications }}">0</h2>
        </div>
      </div>
    </div>
  </div>

  <!-- ========= 2.1 Tips & Info ========= -->
  <div class="row mb-5">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">
          <i class="fas fa-info-circle text-secondary me-2"></i>Tips &amp; Informasi
        </div>
        <div class="card-body p-0">
          <div class="accordion" id="tipsAccordion">
            <!-- Tip 1 -->
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapseOne">
                  Bagaimana Cara Melihat Detail Lowongan?
                </button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse"
                   data-bs-parent="#tipsAccordion">
                <div class="accordion-body">
                  Klik tombol “Detail” pada setiap kartu lowongan untuk melihat informasi lengkap (deskripsi,
                  persyaratan, deadline, dan lainnya).
                </div>
              </div>
            </div>
            <!-- Tip 2 -->
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                  Kapan Waktu Terbaik untuk Mengajukan Lamaran?
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse"
                   data-bs-parent="#tipsAccordion">
                <div class="accordion-body">
                  Usahakan mengajukan lamaran minimal satu minggu sebelum deadline. Perhatikan juga badge
                  “Upcoming Deadlines” di atas untuk lowongan yang segera berakhir.
                </div>
              </div>
            </div>
            <!-- Tip 3 -->
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapseThree">
                  Bagaimana Menghubungi Perusahaan?
                </button>
              </h2>
              <div id="collapseThree" class="accordion-collapse collapse"
                   data-bs-parent="#tipsAccordion">
                <div class="accordion-body">
                  Setelah status lamaran berubah ke “Under Review”, Anda dapat melihat detail kontak HR di halaman
                  detail lowongan. Pastikan email dan CV Anda selalu terbarui.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========= 3. Deadlines & Recent ========= -->
<div class="row g-4 fade-slide">
  <!-- Upcoming Deadlines -->
  <div class="col-12 col-lg-5">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white border-0 fw-semibold">
        <i class="fas fa-calendar-alt text-danger me-2"></i>
        Upcoming Deadlines (<span class="text-danger">≤ 7 hari</span>)
      </div>
      <div class="card-body p-0">
        @if($upcomingDeadlines->isEmpty())
          <div class="text-center text-secondary py-5">
            <i class="fas fa-check-circle fa-2x mb-2"></i>
            <p class="mb-0">Tidak ada deadline dalam 7 hari ke depan.</p>
          </div>
        @else
          <ul id="upcoming-list" class="list-group list-group-flush">
            @foreach($upcomingDeadlines as $low)
              <li
                class="list-group-item d-flex justify-content-between align-items-center detail-link"
                data-url="{{ route('rekomendasi.show', ['lowongan_id' => $low->lowongan_id]) }}"
              >
                <div class="pe-2">
                  <div class="fw-semibold">{{ $low->judul }}</div>
                  <small class="text-secondary">{{ $low->perusahaan->nama }}</small>
                </div>
                <span class="badge deadline-badge">
                  {{ \Carbon\Carbon::parse($low->deadline_lowongan)->format('d M') }}
                </span>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>

    <!-- Recent Applications -->
    <div class="col-12 col-lg-7">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 fw-semibold">
          <i class="fas fa-briefcase text-info me-2"></i>Recent Applications
        </div>
        <div class="card-body p-0">
          @if($recentApplications->isEmpty())
            <div class="text-center text-secondary py-5">
              <i class="fas fa-folder-open fa-2x mb-2"></i>
              <p class="mb-0">Belum ada aplikasi.</p>
            </div>
          @else
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="ps-4">#</th>
                    <th>Judul Lowongan</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($recentApplications as $idx => $app)
                    <tr class="border-0">
                      <td class="ps-4">{{ $idx + 1 }}</td>
                      <td class="fw-semibold">{{ $app->lowongan->judul }}</td>
                      <td>
                      @switch($app->status)
                        @case('pending')
                          <span class="badge bg-warning bg-opacity-25 text-dark">Pending</span>
                          @break

                        @case('diterima')
                          <span class="badge bg-success bg-opacity-25 text-dark">Diterima</span>
                          @break

                        @case('selesai')
                          <span class="badge bg-primary bg-opacity-25 text-dark">Selesai</span>
                          @break

                        @case('ditolak')
                          <span class="badge bg-danger bg-opacity-25 text-dark">Ditolak</span>
                          @break

                        @default
                          <span class="badge bg-secondary bg-opacity-25 text-dark">
                            {{ ucfirst($app->status) }}
                          </span>
                        @endswitch
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- ========= 3.5 Quick Links ========= -->
  <div class="row mb-5">
    <div class="col-12">
      <div class="card border-0 shadow-sm p-4">
        <h5 class="mb-3">Quick Links</h5>
        <div class="d-flex flex-wrap gap-3">
          <a
            href="https://docs.google.com/document/d/1mfUl2jg4i6oLN1PEhcNnv5sijADaRgJO4EYLJhqsz24/edit?usp=sharing"
            class="btn btn-sm btn-secondary"
            target="_blank"
            rel="noopener noreferrer"
          >
            <i class="fas fa-book me-1"></i> Panduan Penggunaan
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- ========= 4. Modal ========= -->
  <div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content"></div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script>
  // ===== Counter Animation =====
  const animateCounter = (el) => {
    const target = +el.dataset.count;
    const increment = Math.ceil(target / 60);
    let current = 0;
    const step = () => {
      current += increment;
      if (current >= target) {
        el.textContent = target;
      } else {
        el.textContent = current;
        requestAnimationFrame(step);
      }
    };
    requestAnimationFrame(step);
  };

  // ===== IntersectionObserver for fade-slide =====
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('show');
        entry.target.querySelectorAll('.counter').forEach(animateCounter);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });

  document.querySelectorAll('.fade-slide').forEach(el => observer.observe(el));

  // ===== CSRF Setup for AJAX =====
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

  // ===== Load detail via AJAX =====
  $(document).on('click', '.detail-link', function(e) {
    e.preventDefault();
    const url = $(this).data('url');
    $('#myModal .modal-content').load(url, () => $('#myModal').modal('show'));
  });

  // ===== Welcome Toast =====
  document.addEventListener('DOMContentLoaded', () => {
    if (!sessionStorage.getItem('welcomeToastShown')) {
      const toastHtml = `
        <div class="toast align-items-center text-white bg-primary border-0 position-fixed top-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">
              Welcome back, {{ Auth::user()->name }}! Ready to find your next internship?
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>
        </div>`;
      document.body.insertAdjacentHTML('beforeend', toastHtml);
      const toastEl = document.querySelector('.toast');
      const bsToast = new bootstrap.Toast(toastEl, { delay: 4500 });
      bsToast.show();
      sessionStorage.setItem('welcomeToastShown', 'true');
    }
  });

    $(document).ready(function() {
    $('#upcoming-list').on('click', '.detail-link', function() {
      const targetUrl = $(this).data('url');
      if (targetUrl) {
        window.location.href = targetUrl;
      }
    });
  });
</script>
@endpush
