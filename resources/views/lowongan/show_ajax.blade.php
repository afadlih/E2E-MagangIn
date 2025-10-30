{{-- resources/views/lowongan/show_ajax.blade.php --}}

@empty($lowongan)
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Kesalahan</h5></div>
      <div class="modal-body">
        <div class="alert alert-danger">Data tidak ditemukan.</div>
      </div>
    </div>
  </div>
@else
  @php
    // Tentukan URL sylabus (link eksternal ATAU file di storage)
    $sylabusUrl = null;
    if ($lowongan->sylabus_path) {
      $isExternal = \Illuminate\Support\Str::startsWith(
        $lowongan->sylabus_path,
        ['http://', 'https://']
      );

      $sylabusUrl = $isExternal
        ? $lowongan->sylabus_path                   // full URL
        : asset('storage/' . $lowongan->sylabus_path); // file lokal
    }
  @endphp

  <div class="modal-header">
    <h5 class="modal-title">Detail Lowongan</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"></button>
  </div>

  <div class="modal-body">
    <table class="table table-sm table-bordered mb-0">
      <tr><th style="width:150px">Judul</th><td>{{ $lowongan->judul }}</td></tr>
      <tr><th>Deskripsi</th><td>{{ $lowongan->deskripsi }}</td></tr>
      <tr><th>Tanggal Mulai</th><td>{{ $lowongan->tanggal_mulai_magang->format('d-m-Y') }}</td></tr>
      <tr><th>Deadline</th><td>{{ $lowongan->deadline_lowongan->format('d-m-Y') }}</td></tr>
      <tr><th>Lokasi</th><td>{{ $lowongan->provinsi->alt_name ?? '-' }}</td></tr>
      <tr><th>Perusahaan</th><td>{{ $lowongan->perusahaan->nama ?? '-' }}</td></tr>
      <tr><th>Periode</th><td>{{ $lowongan->periode->periode ?? '-' }}</td></tr>
      <tr>
        <th>Sylabus</th>
        <td>
          @if($sylabusUrl)
            <a href="{{ $sylabusUrl }}" target="_blank">Unduh / Buka Sylabus</a>
          @else
            -
          @endif
        </td>
      </tr>
      <tr><th>Status</th><td>{{ ucfirst($lowongan->status) }}</td></tr>
      <tr><th>Kuota</th><td>{{ $lowongan->kuota }} mahasiswa</td></tr>
      <tr><th>Durasi</th><td>{{ $lowongan->durasi }}</td></tr>
      <tr><th>Tipe Bekerja</th><td>{{ $lowongan->tipe_bekerja }}</td></tr>
    </table>

    {{-- Preview PDF jika ada --}}
    @if($sylabusUrl)
      <div class="border rounded mt-3" style="height:400px">
        <embed src="{{ $sylabusUrl }}#toolbar=1&navpanes=0&scrollbar=1"
               type="application/pdf"
               width="100%"
               height="100%"
               onerror="this.outerHTML='<p class=&quot;text-danger p-3&quot;>Browser tidak dapat menampilkan PDF. Gunakan link di atas.</p>'">
      </div>
    @endif
  </div>

  <div class="modal-footer">
    <button class="btn btn-warning btn-sm"
            onclick="modalAction('{{ url('/lowongan/'.$lowongan->lowongan_id.'/edit_ajax') }}')">
      Edit
    </button>
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
  </div>
@endempty
