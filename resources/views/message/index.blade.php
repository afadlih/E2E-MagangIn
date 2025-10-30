@extends('layouts.template_mhs')

@section('content')

<div class="card">
    {{-- <div class="card-header">
        <h4 class="card-title">Daftar Notifikasi</h4>
    </div> --}}
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

        <table id="message-table" class="display table table-striped table-hover" style="width:100%">
            <thead class="thead-dark">
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Pesan</th>
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#message-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('/message/list') }}",
                type: "POST"
            },
            columns: [
                { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false, width: "5%" },
                { data: 'judul' },
                { data: 'pesan' },
                { data: 'waktu_dibuat', width: "15%" },
                { data: 'aksi', className: 'text-center', orderable: false, searchable: false, width: "13%" }
            ],
            rowCallback: function(row, data) {
                if (data.status_baca === 0 || data.status_baca === '0') {
                    $(row).css('background-color', '#f0f8ff'); // biru muda
                    $(row).addClass('fw-bold'); // bold tulisan (pastikan class ini tersedia)
                }
            }
        });
    });

    function modalAction(url = '') {
        $('#myModal .modal-content').load(url, function () {
            $('#myModal').modal('show');
        });
    }
    
    function markAsRead(notifikasi_id) {
        $.ajax({
            url: '{{ url('/') }}' + '/message/' + notifikasi_id + '/mark_as_read',
            type: 'post',
            success: function(response) {
                Swal.fire({
                    icon: response.status ? 'success' : 'warning',
                    title: response.message
                });

                if (response.status) {
                    $('#message-table').DataTable().ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message ?? xhr.responseText ?? 'Terjadi kesalahan tak dikenal';
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan',
                    text: errorMessage
                });
            }
        });
    }

</script>
@endpush
