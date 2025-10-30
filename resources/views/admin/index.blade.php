@extends('layouts.template')

@section('content')
<div class="card">
  <div class="card-header">
    <div class="d-flex gap-2 align-items-center flex-wrap">
      <button onclick="modalAction('{{ url('/admin/import') }}')" class="btn btn-info">
        Import Admin
      </button>
      <a href="{{ url('/admin/export_excel') }}" class="btn btn-primary">
        <i class="fa fa-file-excel"></i> Export Admin
      </a>
      <a href="{{ url('/admin/export_pdf') }}" class="btn btn-warning">
        <i class="fa fa-file-pdf"></i> Export Admin
      </a>
      <button class="btn btn-primary btn-round ms-auto" onclick="modalAction('{{ url('/admin/create_ajax') }}')">
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
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"></div>
      </div>
    </div>

    <div class="card-body">
      <table id="admin-table" class="display table table-striped table-hover" style="width: 100%">
        <thead class="thead-dark">
          <tr>
            <th>No.</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Telepon</th>
            <th style="width: 15%">Aksi</th>
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

    $('#admin-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ url('/admin/list') }}",
        type: "POST"
      },
      columns: [
        { data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false, width: "5%" },
        { data: 'nama' },
        { data: 'email' },
        { data: 'telp' },
        { data: 'aksi', className: "text-center", orderable: false, searchable: false, width: "15%" }
      ]
    });
  });

  function modalAction(url = '') {
    $('#myModal .modal-content').load(url, function () {
      $('#myModal').modal('show');
    });
  }
</script>
@endpush
