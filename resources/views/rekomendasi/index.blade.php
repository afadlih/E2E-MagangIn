{{-- resources/views/rekomendasi/index.blade.php --}}
@extends('layouts.template_mhs')

@section('content')

{{-- Profile header card --}}
<div class="card mb-4">
  </div>
  <div class="card-body">
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Profile Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog"
         aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
      </div>
    </div>
  </div>
</div>



<div class="container mt-4">

  {{-- ============================= --}}
  {{-- DEBUG: Tampilkan data mahasiswa  --}}
  {{-- ============================= --}}
  {{-- <div class="card mb-4 border-danger">
    <div class="card-header bg-danger text-white">
      <strong>DEBUG: Data Mahasiswa (User ID: {{ Auth::id() }})</strong>
    </div>
    <div class="card-body text-dark">
      <p><strong>Nama:</strong> {{ $mhs->full_name ?? '-' }}</p>
      <p><strong>Pref (Bidang):</strong> {{ $mhs->pref ?? '-' }}</p>
      <p><strong>Skill:</strong> {{ $mhs->skill ?? '-' }}</p>
      <p><strong>Lokasi Preferensi:</strong> {{ $mhs->lokasi ?? '-' }}</p>
      <p><strong>Gaji Minimum:</strong> Rp {{ number_format($mhs->gaji_minimum, 0, ',', '.') }}</p>
      <p><strong>Durasi Preferensi:</strong> {{ $mhs->durasi }} bulan</p>
    </div>
  </div> --}}
  {{-- ===================================== --}}

  {{-- Filter form --}}
<form id="filter-form" class="card mb-4 p-3 bg-dark text-white">
  <div class="row g-3">

    {{-- 1) Posisi / Jabatan as dropdown --}}
    <div class="col-md-2">
      <select name="posisi" class="form-select">
        <option value="">— Semua Posisi —</option>
        @foreach($allBidang as $bidang)
          <option
            value="{{ $bidang->nama }}"
            {{ request('posisi') === $bidang->nama ? 'selected' : '' }}
          >
            {{ $bidang->nama }}
          </option>
        @endforeach
      </select>
    </div>

      {{-- 2) Skill yang cocok → dropdown multi-checkbox --}}
      <div class="col-md-2">
@php
  $selected = request('skills', []);
@endphp

<div class="dropdown">
  <button
    class="btn btn-light dropdown-toggle w-100 text-start"
    type="button"
    id="skillsDropdown"
    data-bs-toggle="dropdown"
    aria-expanded="false"
  >
    Skill yang cocok
    @if(count($selected))
      ({{ count($selected) }})
    @endif
  </button>

  <div
    class="dropdown-menu p-3"
    aria-labelledby="skillsDropdown"
    style="max-height: 200px; overflow-y: auto;"
    data-bs-auto-close="outside"
  >
    @foreach($allSkills as $skill)
      <div class="form-check">
        <input
          class="form-check-input"
          type="checkbox"
          name="skills[]"
          id="skill_{{ \Str::slug($skill->nama) }}"
          value="{{ $skill->nama }}"
          {{ in_array($skill->nama, $selected) ? 'checked' : '' }}
        >
        <label
          class="form-check-label"
          for="skill_{{ \Str::slug($skill->nama) }}"
        >
          {{ $skill->nama }}
        </label>
      </div>
    @endforeach
  </div>
</div>
      </div>

      {{-- 3) Lokasi --}}
      <div class="col-md-2">
        <select name="lokasi" id="lokasi" class="form-select">
          <option value="">— Semua Lokasi —</option>
          @foreach($provinsi as $prov)
            <option value="{{ $prov->alt_name }}"
              {{ request('lokasi') == $prov->alt_name ? 'selected' : '' }}>
              {{ $prov->alt_name }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- 4) Tipe Bekerja --}}
      <div class="col-md-2">
        <select name="tipe_bekerja" class="form-select">
          <option value="">Tipe Bekerja</option>
          <option value="remote"  {{ request('tipe_bekerja') === 'remote'  ? 'selected' : '' }}>Remote</option>
          <option value="on_site" {{ request('tipe_bekerja') === 'on_site' ? 'selected' : '' }}>On-Site</option>
          <option value="hybrid"  {{ request('tipe_bekerja') === 'hybrid'  ? 'selected' : '' }}>Hybrid</option>
        </select>
      </div>

      {{-- 5) Durasi --}}
      <div class="col-md-2">
        <select name="durasi" class="form-select">
          <option value="">Durasi (bulan)</option>
          <option value="3" {{ request('durasi') == '3' ? 'selected' : '' }}>3</option>
          <option value="6" {{ request('durasi') == '6' ? 'selected' : '' }}>6</option>
        </select>
      </div>

      {{-- 6) Tombol Cari --}}
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">
          <i class="fas fa-search"></i> Cari
        </button>
      </div>
    </div>
  </form>
  {{-- Daftar hasil (akan di‐inject via AJAX) --}}
  <div class="row" id="rekomendasi-list">
    @include('rekomendasi.partials.list', ['lowongan' => $lowongan, 'mhs' => $mhs])
  </div>
</div>
@endsection

@push('js')
<script>
  // This script runs after jQuery is loaded (the layout defines @stack('js') after jQuery).
  $(function() {
    // 1) Intercept the filter form submit
    $('#filter-form').on('submit', function(e) {
      e.preventDefault();
      const url  = window.location.href.split('?')[0];
      const data = $(this).serialize();

      $.ajax({
        url: url,
        method: 'GET',
        data: data,
        dataType: 'json',
        success: function(response) {
          $('#rekomendasi-list').html(response.html);

          // Update URL so filters appear in address bar
          // const newUrl = url + '?' + data;
          // window.history.pushState(null, '', newUrl);
        },
        error: function(err) {
          console.error('Error loading recommendations:', err);
          // If AJAX fails, do a full reload:
          // window.location.href = url + '?' + data;
        }
      });
    });

    // 2) Intercept “Lihat Detail” clicks inside the list
    $('#rekomendasi-list').on('click', '.detail-link', function(e) {
      e.preventDefault();

      // Base detail URL, e.g. "/mahasiswa/rekomendasi/4"
      let baseUrl = $(this).data('url');
      // Append ?ajax=1 so the controller returns JSON
      let ajaxUrl = baseUrl + '?ajax=1';

      $.ajax({
        url: ajaxUrl,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
          // Swap out the entire <body> with the detail page HTML:
          $('body').html(response.html);

          // Clean the URL (remove ?ajax=1)
          // window.history.pushState(null, '', baseUrl);
        },
        error: function(err) {
          console.error('Error loading detail via AJAX:', err);
          // Fallback: full navigation
          window.location.href = baseUrl;
        }
      });
    });
  });
</script>
{{-- <script>
  // submit filter form → reload page dengan query param
  $('#filter-form').on('submit', function(e){
    e.preventDefault();
    const loc = $('#lokasi').val();
    const url = new URL(window.location.href);
    if (loc) url.searchParams.set('lokasi', loc);
    else url.searchParams.delete('lokasi');
    window.location.href = url.toString();
  });
</script> --}}
@endpush
