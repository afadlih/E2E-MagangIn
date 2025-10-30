@extends('layouts.template_dsn')

@section('content')
<div class="card">
  <div class="card-body">
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Modal untuk detail mahasiswa --}}
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"></div>
      </div>
    </div>

    {{-- Filter Mahasiswa --}}
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

    {{-- Tabel Mahasiswa Bimbingan --}}
    <table id="bimbingan-table" class="display table table-striped" style="width:100%">
      <thead class="thead-dark">
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>NIM</th>
          <th>Prodi</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
@endsection

@push('js')
<script>
  $(function () {
    // Setup CSRF token untuk AJAX
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    // Inisialisasi DataTables
    var table = $('#bimbingan-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ url('/mahasiswa-bimbingan/list') }}",
        type: "POST",
        data: function(d) {
          d.prodi_id = $('#prodi_id').val();
        }
      },
      columns: [
        { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
        { data: 'full_name' },
        { data: 'mhs_nim' },
        { data: 'prodi' },
        { data: 'status_bimbingan' },
        { data: 'aksi', className: 'text-center', orderable: false, searchable: false }
      ],
    });

    // Reload DataTables saat filter berubah
    $('#prodi_id').change(function () {
    table.ajax.reload();
  });
  });

  // Fungsi buka modal
  function modalAction(url = '') {
    $('#myModal .modal-content').load(url, function () {
      $('#myModal').modal('show');
    });
  }
</script>
@endpush
