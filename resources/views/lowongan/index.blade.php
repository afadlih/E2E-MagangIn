@extends('layouts.template')

@push('css')
<style>
  /* Responsive table wrapper */
  .table-responsive { overflow-x: auto; }

  /* Truncate long text with ellipses */
  .text-truncate {
    max-width: 120px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Tighter padding and smaller font */
  #lowongan-table td,
  #lowongan-table th {
    padding: .3rem .5rem;
    font-size: .875rem;
  }
</style>
@endpush

@section('content')
<div class="card">
  <div class="card-header d-flex">
    <h3 class="card-title">Manajemen Lowongan Magang</h3>
    <button class="btn btn-primary ms-auto" onclick="modalAction('{{ url('/lowongan/create_ajax') }}')">
      <i class="fa fa-plus"></i> Tambah
    </button>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table id="lowongan-table" class="table table-striped nowrap" style="width:100%">
        <thead class="thead-dark">
          <tr>
            <th>No.</th>
            <th>Judul</th>
            <th>Deskripsi</th>
            <th>Perusahaan</th>
            <th>Lokasi</th>
            <th>Mulai</th>
            <th>Deadline</th>
            <th>Periode</th>
            <th>Status</th>
            <th>Kuota</th>
            <th>Durasi</th>
            <th>Tipe Bekerja</th>
            <th>Aksi</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content"></div>
  </div>
</div>
@endsection

@push('js')
<script>
$(function(){
  // CSRF token for AJAX
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

  $('#lowongan-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ url('/lowongan/list') }}",
      type: "POST"
    },
    scrollX: true,
    responsive: true,
    autoWidth: false,
    columns: [
      { data: 'DT_RowIndex',      name: 'DT_RowIndex',   orderable: false, searchable: false },
      { data: 'judul',            name: 'judul' },
      { 
        data: 'deskripsi',        
        name: 'deskripsi',     
        className: 'text-truncate', 
        width: '150px',
        render: function (data) {
          // Batasi panjang teks menjadi 50 karakter
          const maxLength = 50;
          if (data && data.length > maxLength) {
            return `<span title="${data.replace(/"/g, '&quot;')}" data-toggle="tooltip">${data.substring(0, maxLength)}...</span>`;
          }
          return data;
        }
      },
      { data: 'perusahaan',       name: 'perusahaan',    orderable: false, searchable: false, className: 'text-truncate', width: '120px' },
      { data: 'lokasi',           name: 'lokasi' },
      {
        data: 'tanggal_mulai_magang',
        name: 'tanggal_mulai_magang',
        render: function(data, type) {
          if (!data) return '';
          const [y, m, d] = data.split('T')[0].split('-');
          return type === 'display' ? `${d}-${m}-${y}` : `${y}-${m}-${d}`;
        }
      },
      {
        data: 'deadline_lowongan',
        name: 'deadline_lowongan',
        render: function(data, type) {
          if (!data) return '';
          const [y, m, d] = data.split('T')[0].split('-');
          return type === 'display' ? `${d}-${m}-${y}` : `${y}-${m}-${d}`;
        }
      },
      { data: 'periode',          name: 'periode',       orderable: false, searchable: false },
      { data: 'status',           name: 'status' },
      { data: 'kuota',            name: 'kuota' },
      { data: 'durasi',           name: 'durasi' },
      { data: 'tipe_bekerja',     name: 'tipe_bekerja' },
      { data: 'aksi',             name: 'aksi',          orderable: false, searchable: false }
    ],
    columnDefs: [
      { targets: '_all', defaultContent: '' }
    ],
    language: {
      searchPlaceholder: "Search…"
    },
    // Inisialisasi tooltip untuk Bootstrap
    drawCallback: function () {
      $('[data-toggle="tooltip"]').tooltip();
    }
  });
});

function modalAction(url = '') {
  $('#myModal .modal-content')
    .load(url, function(){ $('#myModal').modal('show'); });
}

const lowonganBase = "{{ url('lowongan') }}";
function deleteLowongan(id) {
  if (!id) return;
  $.ajax({
    url: `${lowonganBase}/${id}/delete_ajax`,
    type: 'DELETE',
    success(res) {
      if (res.status) {
        $('#myModal').modal('hide');
        $('#lowongan-table').DataTable().ajax.reload();
      } else {
        alert(res.message || 'Gagal menghapus data.');
      }
    },
    error(xhr) {
      console.error('DELETE error:', xhr);
      alert('Terjadi kesalahan pada server.');
    }
  });
}
</script>
@endpush