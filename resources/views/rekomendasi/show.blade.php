@php
  use Illuminate\Support\Str;
@endphp

{{-- resources/views/rekomendasi/show.blade.php --}}
@extends('layouts.template_mhs')

@section('content')
  <div class="container mt-4" id="single-detail">
    {{-- Modal Container --}}
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
    data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content"></div>
    </div>
    </div>
    {{-- Statistik ringkas --}}
    <div class="d-flex mb-4">
    <div class="me-4"><strong>Total Posisi:</strong> {{ $totalPositions }}</div>
    <div class="me-4"><strong>Total Perusahaan:</strong> {{ $totalCompanies }}</div>
    <div><strong>Total Job:</strong> {{ $totalJobs }}</div>
    </div>

    <div class="row">
    {{-- ====================== SIDEBAR KIRI ====================== --}}
    <div class="col-md-4 mb-4">
      <h6 class="mb-2">Lowongan Lainnya</h6>

      <div class="overflow-auto" style="max-height:calc(100vh - 220px);">
      @foreach($lowonganList as $l)
      <a href="{{ route('rekomendasi.show', $l->lowongan_id) }}" class="text-decoration-none sidebar-link"
        data-url="{{ route('rekomendasi.show', $l->lowongan_id) }}">
        <div class="card shadow-sm mb-4 sidebar-card
      {{ $l->lowongan_id == $lowongan->lowongan_id ? 'border-primary' : '' }}">
        <div class="card-body text-center p-4">
        @if($l->perusahaan->logo_path)
        <img src="{{ asset('uploads/logos/' . $l->perusahaan->logo_path) }}" alt="Logo {{ $l->perusahaan->nama }}"
        class="img-fluid mb-3" style="max-height:90px">
      @endif

        <h6 class="text-muted mb-1 text-truncate">{{ $l->perusahaan->nama }}</h6>
        <strong class="d-block mb-1 text-truncate">{{ $l->judul }}</strong>

        <small class="text-secondary d-block mb-1">
        <i class="fas fa-map-marker-alt"></i> {{ $l->provinsi->alt_name ?? '-' }}
        </small>

        <div class="small mb-2">
        <span class="badge bg-success">Umum</span>
        <span class="badge bg-secondary">{{ $l->durasi ?? '-' }} bln</span>
        @php
  // Normalize and human-readable text, e.g. "on_site" → "On Site"
      $typeText = ucfirst(str_replace('_', ' ', $l->tipe_bekerja));
    @endphp

    @switch($l->tipe_bekerja)
      @case('on_site')
        <span class="badge bg-primary">{{ $typeText }}</span>
        @break

      @case('remote')
        <span class="badge bg-success">{{ $typeText }}</span>
        @break

      @case('hybrid')
        <span class="badge bg-warning text-dark">{{ $typeText }}</span>
        @break

      @default
        <span class="badge bg-secondary">{{ $typeText }}</span>
    @endswitch
        </div>

        <small class="text-danger d-block">
        Penutupan: {{ $l->deadline_lowongan->format('d M Y') }}
        </small>
        </div>
        </div>
      </a>
    @endforeach
      </div>
    </div>

    {{-- ====================== DETAIL KANAN ====================== --}}
    <div class="col-md-8">
      <div class="card h-100 shadow-sm border-0">
      <div class="card-body">
        {{-- Ringkasan posisi & pelamar --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <span class="badge bg-success">Umum</span>
          <small class="ms-2">
          {{ $lowongan->kuota }} Posisi &bull; {{ $lowongan->lamaran->count() }} Pelamar
          </small>
        </div>
      
      @php
    // Assuming $lowongan->mahasiswa is the student object and file_cv is the field
    $hasCv = !empty($lowongan->mahasiswa->file_cv);
@endphp

@php
    // Assuming $lowongan->mahasiswa is the student object
    // Initialize array to store missing fields
    $missingFields = [];
    
    // Check each required field
    if (empty(Auth::user()->mahasiswa->mhs_nim)) {
        $missingFields[] = 'NIM';
    }
    if (empty(Auth::user()->mahasiswa->user_id)) {
        $missingFields[] = 'User ID';
    }
    if (empty(Auth::user()->mahasiswa->full_name)) {
        $missingFields[] = 'Nama Lengkap';
    }
    if (empty(Auth::user()->mahasiswa->alamat)) {
        $missingFields[] = 'Alamat';
    }
    if (empty(Auth::user()->mahasiswa->telp)) {
        $missingFields[] = 'Nomor Telepon';
    }
    if (empty(Auth::user()->mahasiswa->prodi_id)) {
        $missingFields[] = 'Program Studi';
    }
    if (!empty(Auth::user()->mahasiswa->file_cv)) { // Assuming file_cv is part of the student data
        $missingFields[] = 'CV';
    }
    if (!empty(Auth::user()->mahasiswa->lokasi)) {
        $missingFields[] = 'lokasi';
    }
    // if (empty(Auth::user()->mahasiswa->t_minat_mahasiswa->bidang_keahlian_id)) {
    //     $missingFields[] = 'Bidang Keahlian';
    // }



    // Determine if all data is filled
    $allDataFilled = empty($missingFields);
@endphp

@if($allDataFilled)
<button onclick="loadModal('{{ url('rekomendasi/' . $lowongan->lowongan_id . '/create_ajax') }}', '{{ $lowongan->lowongan_id }}')" class="btn btn-primary">
  Daftar Sekarang
</button>
@else
    <!-- Button to trigger the modal -->
    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#missingDataModal">
        Harus Lengkapi Data
    </button>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="missingDataModal" tabindex="-1" aria-labelledby="missingDataModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="missingDataModalLabel">Data Tidak Lengkap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tidak dapat mendaftar. Harap lengkapi data berikut:</p>
                    <ul>
                        @foreach($missingFields as $field)
                            <li>{{ $field }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    
                </div>
            </div>
        </div>
    </div>
@endif
        </div>

        {{-- Judul & perusahaan --}}
        <h4>{{ $lowongan->judul }}</h4>
        <h6 class="text-muted">{{ $lowongan->perusahaan->nama }}</h6>
        <p class="text-secondary mb-4">
        <small class="text-secondary d-block mb-1">
          <i class="fas fa-map-marker-alt"></i>
          {{ $lowongan->provinsi->alt_name ?? '-' }}
        </small>

        @php
          // Humanize “on_site” → “On Site”, etc.
          $typeText = ucfirst(str_replace('_',' ',$lowongan->tipe_bekerja));
        @endphp

        <small class="text-secondary d-block mb-3">
          <i class="fas fa-briefcase"></i>
          {{ $typeText }}
        </small>

        {{-- Tabs Deskripsi / Perusahaan --}}
        <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
          <a class="nav-link active" data-bs-toggle="tab" href="#deskripsi">Deskripsi Lowongan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="tab" href="#perusahaan">Perusahaan</a>
        </li>
        </ul>

        <div class="tab-content">
        {{-- Tab Deskripsi --}}
        <div class="tab-pane fade show active" id="deskripsi">
          <h5><i class="fas fa-info-circle"></i> Rincian Lowongan</h5>
          <p>{!! nl2br(e($lowongan->deskripsi)) !!}</p>

          <h5 class="mt-4"><i class="fas fa-file-pdf"></i> Silabus</h5>
          @if($lowongan->sylabus_path)
        @php
        // Determine if we have a full URL or a storage path
        $sylabusUrl = Str::startsWith($lowongan->sylabus_path, ['http://', 'https://'])
        ? $lowongan->sylabus_path
        : asset('storage/' . $lowongan->sylabus_path);
      @endphp

        <a href="{{ $sylabusUrl }}" target="_blank" class="btn btn-outline-primary mb-4">
        <i class="fas fa-download"></i> Unduh Silabus
        </a>
      @else
        <p class="text-muted">Tidak ada silabus tersedia.</p>
      @endif

          <h5 class="mt-4"><i class="fas fa-calendar-alt"></i> Tanggal Penting</h5>
          <ul>
          <li>Durasi: {{ $lowongan->durasi }} bulan</li>
          <li>Penutupan lamaran: {{ $lowongan->deadline_lowongan->format('d M Y') }}</li>
          <li>Pengumuman: {{ optional($lowongan->pengumuman)->format('d M Y') ?? '-' }}</li>
          </ul>
        </div>

        {{-- Tab Perusahaan --}}
        <div class="tab-pane fade" id="perusahaan">
          <h5><i class="fas fa-building"></i> {{ $lowongan->perusahaan->nama }}</h5>

          <p>
            <strong><i class="fas fa-map-marker-alt"></i> Alamat:</strong><br>
            {{ $lowongan->perusahaan->alamat ?? '—' }}
          </p>

          <p>
            <strong><i class="fas fa-envelope"></i> Email:</strong><br>
            @if($lowongan->perusahaan->email)
              <a href="mailto:{{ $lowongan->perusahaan->email }}">
                {{ $lowongan->perusahaan->email }}
              </a>
            @else
              —
            @endif
          </p>

          <p>
            <strong><i class="fas fa-phone"></i> Telepon:</strong><br>
            @if($lowongan->perusahaan->telp)
              <a href="tel:{{ $lowongan->perusahaan->telp }}">
                {{ $lowongan->perusahaan->telp }}
              </a>
            @else
              —
            @endif
          </p>
        </div>


        {{-- Share link --}}
        <div class="mt-4">
        <div class="dropdown">
          <a class="text-decoration-none dropdown-toggle" href="#" role="button" id="shareDropdown"
          data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-share-alt"></i> Bagikan Lowongan
          </a>
          <ul class="dropdown-menu" aria-labelledby="shareDropdown">
          <li>
            <a class="dropdown-item"
            href="https://api.whatsapp.com/send?text={{ urlencode($lowongan->judul . ' di ' . $lowongan->perusahaan->nama . ' ' . url()->current()) }}"
            target="_blank">
            <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
          </li>
          <li>
            <a class="dropdown-item"
            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
            target="_blank">
            <i class="fab fa-facebook"></i> Facebook
            </a>
          </li>
          <li>
            <button class="dropdown-item" onclick="copyLink()">
            <i class="fas fa-link"></i> Salin Tautan (Instagram)
            </button>
          </li>
          </ul>
        </div>
        </div>
      </div>
      </div>
    </div>
    </div>
  </div>
@endsection

@push('js')
  <script>
    function loadModal(url) {
      // 1. Ambil lowongan_id dari URL
      const pathArray = window.location.pathname.split('/');
      const lowonganId = pathArray[pathArray.length - 1];

      // 2. Cek status lamaran melalui AJAX
      $.ajax({
        url: '{{ url("rekomendasi/check-status") }}/' + lowonganId,
        method: 'GET',
        dataType: 'json',
        success: function (response) {
          if (response.status === 'diterima_existing') {
            toastr.error('Anda sudah diterima di lowongan ' + response.lowongan_judul + ' dan tidak dapat mengajukan magang lagi.', '', {
              "closeButton": true,
              "progressBar": true,
              "positionClass": "toast-top-right",
              "timeOut": "10000",
              "extendedTimeOut": "2000",
              "backgroundColor": "#ff4444",
              "textColor": "#ffffff"
            });
          } else if (response.status === 'selesai') {
            toastr.error('Anda sudah menyelesaikan magang dan tidak dapat mengajukan magang lagi.', '', {
              "closeButton": true,
              "progressBar": true,
              "positionClass": "toast-top-right",
              "timeOut": "10000",
              "extendedTimeOut": "2000",
              "backgroundColor": "#ff4444",
              "textColor": "#ffffff"
            });
          } else if (response.status === 'pending') {
            toastr.error('Anda sudah mengajukan lamaran untuk lowongan ' + response.lowongan_judul + ' dan masih menunggu persetujuan.', '', {
              "closeButton": true,
              "progressBar": true,
              "positionClass": "toast-top-right",
              "timeOut": "10000",
              "extendedTimeOut": "2000",
              "backgroundColor": "#ff4444",
              "textColor": "#ffffff"
            });
          } else {
            // Load modal if status is 'available'
            $('#myModal .modal-content').load(url, function () {
              $('#myModal').modal('show');
              $(document).off('click', '[data-dismiss="modal"]').on('click', '[data-dismiss="modal"]', function () {
                $('#myModal').modal('hide');
              });
            });
          }
        },
        error: function (xhr) {
          toastr.error('Terjadi kesalahan saat memeriksa status lamaran. Status: ' + xhr.status + ' (URL: ' + xhr.responseURL + ')', '', {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
          });
          console.log('AJAX Error:', xhr);
        }
      });
    }

    function modalAction(url = '') {
      $('#myModal .modal-content').load(url, function () {
        $('#myModal').modal('show');
        $(document).off('click', '[data-dismiss="modal"]').on('click', '[data-dismiss="modal"]', function () {
          $('#myModal').modal('hide');
        });
      });
    }

    $(function () {
      // 1) Intercept sidebar‐link clicks (Lowongan Lainnya)
      $('#single-detail').on('click', '.sidebar-link', function (e) {
        e.preventDefault();
        let baseUrl = $(this).data('url');
        let ajaxUrl = baseUrl + '?ajax=1';
        $.ajax({
          url: ajaxUrl,
          method: 'GET',
          dataType: 'json',
          success: function (response) {
            $('body').html(response.html);
            window.history.replaceState(null, '', '/mahasiswa/rekomendasi-magang');
          },
          error: function (err) {
            console.error('Error loading detail via AJAX:', err);
            window.location.href = baseUrl;
          }
        });
      });
    });

    function copyLink() {
      const url = window.location.href;
      navigator.clipboard.writeText(url)
        .then(() => alert('Tautan berhasil disalin ke clipboard!'))
        .catch(err => console.error('Gagal menyalin tautan:', err));
    }
  </script>
@endpush