@extends('layouts.template')

@section('content')
<div class="card">
  <div class="card-header">
    <div class="d-flex gap-2 align-items-center flex-wrap">
      {{-- <button onclick="modalAction('{{ url('/pengajuanMagang/import') }}')" class="btn btn-info">
          Import Pengajuan Magang
      </button> --}}
      <a href="{{ url('/pengajuan-magang/export_excel') }}" class="btn btn-primary">
          <i class="fa fa-file-excel"></i> Export Magang Excel
      </a>
      <a href="{{ url('/pengajuan-magang/export_pdf') }}" class="btn btn-warning">
          <i class="fa fa-file-pdf"></i> Export Magang Pdf
      </a>
      {{-- <button class="btn btn-primary btn-round ms-auto" onclick="modalAction('{{ url('/pengajuanMagang/create_ajax') }}')">
          <i class="fa fa-plus"></i> Tambah Data
      </button> --}}
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
    <div class="table-responsive">
      <table
        id="pengajuan-magang-table"
        class="display table table-striped table-hover"
        style="width: 100%"
      >
        <thead class="thead-dark">
          <tr>
            <th>No. </th>
            <th>Nama Mahasiswa</th>
            <th>NIM</th>
            <th>Prodi</th>
            <th>Nama Perusahaan</th>
            <th>Lowongan</th>
            <th>Tanggal Lamaran</th>
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
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        
    });
    $(document).ready(function() {
        var tablePengajuanMagang = $('#pengajuan-magang-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('pengajuan-magang/list') }}",
                type: "POST",
                data: function (d) {
                    d.prodi_id = $('#filter_prodi_id').val(); // filter di luar modal
                }
            },
            columns: [
                { data: 'DT_RowIndex',  className: "text-center", orderable: false, searchable: false, width: "5%" },
                { data: 'mahasiswa_nama' },
                { data: 'mhs_nim' },
                { data: 'prodi', name: 'prodi.nama_prodi' },
                { data: 'perusahaan_nama' },
                { data: 'lowongan_judul'},
                { data: 'tanggal_lamaran' },
                { data: 'status',
                  className: "text-center",
                  render: function(data, type, row) {
                      if (data === 'diterima') {
                          return '<span class="badge badge-success"><i class="fa fa-check"></i> Diterima</span>';
                      } else if (data === 'ditolak') {
                          return '<span class="badge badge-danger"><i class="fa fa-times"></i> Ditolak</span>';
                      } else if (data === 'pending') {
                          return '<span class="badge badge-warning"><i class="fa fa-clock"></i> pending</span>';
                      } else {
                          return '<span class="badge badge-secondary">' + data + '</span>';
                      }
                  } 
                },
                { data: 'aksi', className: "text-center", orderable: false, searchable: false, width: "20%" }
            ],
            columnDefs: [
            {
                targets: [1, 3], // Kolom mahasiswa_nama dan dosen_nama
                render: function(data, type, row) {
                    return data.length > 30 ? '<span title="' + data + '">' + data.substring(0, 30) + '...' + '</span>' : data;
                }
            }
        ]
        });

        initProdiFilterListener();

        $('#myModal').on('hidden.bs.modal', function () {
            tablePengajuanMagang.ajax.reload(null, false);
            initProdiFilterListener(); // jaga-jaga
        });
    });
    function modalAction(url = ''){
        $('#myModal .modal-content').load(url,function(){
            $('#myModal').modal('show');
        });
    }
    function initProdiFilterListener() {
      $('#filter_prodi_id').off('change').on('change', function () {
          $('#pengajuan-magang-table').DataTable().ajax.reload();
      });
    }

</script>
@endpush