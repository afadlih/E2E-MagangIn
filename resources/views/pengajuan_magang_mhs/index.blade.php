@extends('layouts.template_mhs')

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

    <div class="table-responsive mt-3">
      <table id="pengajuan-magang-table" class="display table table-striped table-hover w-100">
        <thead class="thead-dark">
          <tr>
            <th>No</th>
            <th>NIM</th> {{-- Ubah sesuai urutan data di JS --}}
            <th>Nama Dosen</th> {{-- Ubah sesuai urutan data di JS --}}
            <th>Tanggal Lamaran</th>
            <th>Nama Lowongan</th>
            <th>Status</th>
            <th style="width: 10%">Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@endsection

@push('js')
<script>
  $(document).ready(function () {
    // Pastikan meta tag CSRF token ada di layouts.template
    // Contoh: <meta name="csrf-token" content="{{ csrf_token() }}">
    $.ajaxSetup({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    tablePengajuanMagang = $('#pengajuan-magang-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ url('pengajuan-magang-mhs/list') }}",
        type: "POST"
      },
      columns: [
        { data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false, width: "5%" },
        { data: 'mhs_nim' }, // Sesuai dengan header NIM
        {
          data: 'dosen_nama', // Sesuai dengan header Nama Dosen
          className: "text-center",
          render: function (data, type, row) {
            // Logika untuk menampilkan badge status jika dosen_nama adalah '-'
            if (data === '-') {
              if (row.status === 'pending') {
                return '<span class="badge badge-warning"><i class="fa fa-clock"></i> Pending</span>';
              } else if (row.status === 'ditolak') {
                return '<span class="badge badge-danger"><i class="fa fa-times"></i> Ditolak</span>';
              }
            }
            return '<span>' + data + '</span>';
          }
        },
        { data: 'tanggal_lamaran' },
        { data: 'lowongan_nama'}, // Koma yang hilang sudah ditambahkan di sini
        {
          data: 'status',
          className: "text-center",
          render: function (data) {
            switch (data) {
              case 'diterima':
                return '<span class="badge badge-success"><i class="fa fa-check"></i> Diterima</span>';
              case 'ditolak':
                return '<span class="badge badge-danger"><i class="fa fa-times"></i> Ditolak</span>';
              case 'pending':
                return '<span class="badge badge-warning"><i class="fa fa-clock"></i> Pending</span>';
              case 'selesai': // Tambahkan case untuk 'selesai' jika ada di backend
                return '<span class="badge badge-info"><i class="fa fa-check-double"></i> Selesai</span>';
              default:
                return '<span class="badge badge-secondary">' + data + '</span>';
            }
          }
        },
        { data: 'aksi', className: "text-center", orderable: false, searchable: false, width: "10%" }
      ],
      columnDefs: [
        {
          targets: [1, 3], // Sesuaikan target jika ada perubahan urutan kolom
          render: function (data) {
            return data.length > 30
              ? '<span title="' + data + '">' + data.substring(0, 30) + '...</span>'
              : data;
          }
        }
      ]
    });
  });

  function modalAction(url = '') {
    $('#myModal .modal-content').load(url, function() {
        $('#myModal').modal('show');
        
        // Re-bind the close button event after content loads
        $(document).off('click', '[data-dismiss="modal"]').on('click', '[data-dismiss="modal"]', function() {
            $('#myModal').modal('hide');
        });
    });
}
</script>
@endpush
