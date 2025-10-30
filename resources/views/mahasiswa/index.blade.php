@extends('layouts.template')

@section('content')
<div class="card">
  <div class="card-header">
      <div class="d-flex gap-2 align-items-center flex-wrap">
      <button onclick="modalAction('{{ url('/mahasiswa/import') }}')" class="btn btn-info">
          Import Mahasiswa
      </button>
      <a href="{{ url('/mahasiswa/export_excel') }}" class="btn btn-primary">
          <i class="fa fa-file-excel"></i> Export Mahasiswa
      </a>
      <a href="{{ url('/mahasiswa/export_pdf') }}" class="btn btn-warning">
          <i class="fa fa-file-pdf"></i> Export Mahasiswa
      </a>
      <button class="btn btn-primary btn-round ms-auto" onclick="modalAction('{{ url('/mahasiswa/create_ajax') }}')">
          <i class="fa fa-plus"></i> Tambah Data
      </button>
  </div>
  </div>


  <div class="card-body">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Modal -->
    <div
      class="modal fade"
      id="myModal"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"></div>
      </div>
    </div>

    <div class="card-body">
       <div class="row mb-3">
          <div class="col-md-3">
              <label for="prodi_id" class="form-label">Filter:</label>
                <select id="filter_prodi_id" name="filter_prodi_id" class="form-control">
                  <option value="">- Semua Prodi -</option>
                  @foreach($prodis as $prodi)
                      <option value="{{ $prodi->prodi_id }}">{{ $prodi->nama_prodi }}</option>
                  @endforeach
              </select>
          </div>
      </div>
      <table
        id="mahasiswa-table"
        class="display table table-striped table-hover"
        style="width: 100%"
      >
        <thead class="thead-dark">
          <tr>
            <th>No.</th>
            <th>NIM</th>
            <th>Nama Lengkap</th>
            <th>Prodi</th>
            <th>Angkatan</th>
            <th>Jenis Kelamin</th>
            <th style="width: 10%">Action</th>
          </tr>
        </thead>
        <tfoot>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection

@push('js')
<script>
  $(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var tableMahasiswa = $('#mahasiswa-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('mahasiswa/list') }}",
            type: "POST",
            data: function (d) {
                d.prodi_id = $('#filter_prodi_id').val(); // filter di luar modal
            }
        },
        columns: [
            { data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false, width: "5%" },
            { data: 'nim', name: 'mhs_nim' },
            { data: 'nama', name: 'full_name' },
            { data: 'prodi', name: 'prodi.nama_prodi' }, // tergantung struktur relasi
            { data: 'angkatan', name: 'angkatan' },
            { data: 'jenis_kelamin', name: 'jenis_kelamin', render: function(data) {
                return data === 'L' ? 'Laki-laki' : 'Perempuan';
            }},
            { data: 'aksi', className: "text-center", orderable: false, searchable: false, width: "10%" }
        ]
    });

    initProdiFilterListener();

    $('#myModal').on('hidden.bs.modal', function () {
        tableMahasiswa.ajax.reload(null, false);
        initProdiFilterListener(); // jaga-jaga
    });
  });

  function initProdiFilterListener() {
      $('#filter_prodi_id').off('change').on('change', function () {
          $('#mahasiswa-table').DataTable().ajax.reload();
      });
  }

  function modalAction(url = '') {
      $('#myModal .modal-content').load(url, function () {
          $('#myModal').modal('show');
      });
  }
</script>
@endpush
