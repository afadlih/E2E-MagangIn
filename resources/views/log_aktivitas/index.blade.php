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

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"></div>
      </div>
    </div>

  <div class="col-md-4">
    <label for="mhs_nim" class="form-label">Nama Mahasiswa</label>
    <select name="mhs_nim" id="mhs_nim" class="form-control">
        <option value="">-- Semua Mahasiswa --</option>
        @foreach($mahasiswas as $mhs)
            <option value="{{ $mhs->mhs_nim }}">{{ $mhs->full_name }}</option>
        @endforeach
    </select>
</div>




    <table id="log-table" class="display table table-striped" style="width:100%">
      <thead class="thead-dark">
        <tr>
          <th>No</th>
          <th>Nama Mahasiswa</th>
          <th>Prodi</th>
          <th>Keterangan</th>
          <th>Waktu</th>
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
    // Setup CSRF token untuk semua AJAX
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    // Inisialisasi DataTables
    var table = $('#log-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ url('/log-aktivitas/list') }}", // Pastikan route ini menerima POST
        type: "POST", // WAJIB POST, karena route-nya hanya mendukung POST
        data: function(d) {
            d.prodi_id = $('#prodi_id').val(); // jika kamu sudah punya ini
            d.mhs_nim = $('#mhs_nim').val();
        }
      },
      columns: [
        { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
        { data: 'nama' },
        { data: 'prodi' },
        { data: 'keterangan' },
        { data: 'waktu' },
        { data: 'aksi', className: 'text-center', orderable: false, searchable: false }
      ],
    });

    // Reload table saat filter prodi berubah
    $('#prodi_id').on('change', function () {
      table.ajax.reload();
    });
    $('#mhs_nim').change(function () {
    $('#log-table').DataTable().ajax.reload();
    });
  });

  // Fungsi untuk membuka modal dan load konten dari URL
 function modalAction(url = ''){
        $('#myModal .modal-content').load(url,function(){
            $('#myModal').modal('show');
        });
    }

</script>
@endpush
