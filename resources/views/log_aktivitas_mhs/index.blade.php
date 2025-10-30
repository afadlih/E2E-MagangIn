@extends('layouts.template_mhs')

@section('content')

<div class="card">
    <div class="card-header">
        <div class="d-flex gap-2 align-items-center flex-wrap">
            @if (isset($lamaranId))
                <button class="btn btn-primary btn-round ms-auto" onclick="modalAction('{{ url('/log-aktivitas-mhs/'. $lamaranId .'/create') }}')">
                    <i class="fa fa-plus"></i> Tambah Aktivitas
                </button>

            @elseif(isset($lamaranSelesai))
                <button class="btn btn-primary btn-round ms-auto" onclick="showSuccessMessageSelesai()" >
                    <i class="fa fa-plus"></i> Tambah Aktivitas
                </button>
            @else
                <button class="btn btn-primary btn-round ms-auto" onclick="showErrorMessage()" >
                    <i class="fa fa-plus"></i> Tambah Aktivitas
                </button>
            @endif
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

        <table id="log-table" class="display table table-striped table-hover" style="width:100%">
            <thead class="thead-dark">
                <tr>
                    <th>No</th>
                    {{-- <th>Lamaran ID</th> --}}
                    <th>Keterangan</th>
                    <th>Waktu</th>
                    <th style="width: 10%">Aksi</th>
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
                url: "{{ url('/log-aktivitas-mhs/list') }}", // Pastikan route ini menerima POST
                type: "POST", // WAJIB POST, karena route-nya hanya mendukung POST
            },
            columns: [
                { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false, width: "5%" },
                // { data: 'lamaran_id' },
                { data: 'keterangan' },
                {
                  data: 'waktu',
                  width: "11%",
                  render: function (data) {
                    if (!data || typeof data !== 'string' || !data.includes('-')) return 'Tanggal tidak valid';

                    const [year, month, day] = data.split('-');

                    const bulan = [
                      'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];

                    const namaBulan = bulan[parseInt(month, 10) - 1];
                    return `${parseInt(day, 10)} ${namaBulan} ${year}`;
                  }
                },
                { 
                    data: 'aksi', 
                    className: 'text-center', 
                    orderable: false, 
                    searchable: false,
                    width: "11%",
                }
            ],
        });
    });

    // Fungsi untuk membuka modal dan load konten dari URL
    function modalAction(url = '') {
        $('#myModal .modal-content').load(url, function () {
            $('#myModal').modal('show');
        });
    }
    function showErrorMessage() {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Belum ada magang yang perlu diisi aktivistasnya.',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#3085d6'
        });
    }
    function showSuccessMessageSelesai() {
        Swal.fire({
            icon: 'success',
            title: 'Magang Selesai',
            text: 'Selamat, magang Anda telah selesai. Anda tidak perlu mengisi aktivitas lagi.',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#3085d6'
        });
    }

</script>
@endpush