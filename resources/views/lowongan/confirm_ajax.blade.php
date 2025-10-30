{{-- resources/views/lowongan/confirm_ajax.blade.php --}}
@php $lowongan = $lowongan ?? null; @endphp

@empty($lowongan)
    <div id="myModal" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #1a2e4f; color: white;">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data lowongan tidak ditemukan.
                </div>
                <a href="{{ url('/lowongan') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/lowongan/' . $lowongan->lowongan_id . '/delete_ajax') }}" method="POST" id="form-delete-lowongan">
        @csrf
        @method('DELETE')
        <div class="modal-header" style="background-color: #1a2e4f; color: white;">
            <h5 class="modal-title">Nonaktifkan Lowongan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <h5><i class="icon fas fa-ban"></i> Konfirmasi !!!</h5>
                Apakah Anda yakin ingin menonaktifkan lowongan berikut?
            </div>
            <table class="table table-sm table-bordered table-striped">
                <tr>
                    <th class="text-right col-3">ID Lowongan :</th>
                    <td class="col-9">{{ $lowongan->lowongan_id }}</td>
                </tr>
                <tr>
                    <th class="text-right col-3">Judul Lowongan :</th>
                    <td class="col-9">{{ $lowongan->judul }}</td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-warning">Ya, Nonaktifkan</button>
        </div>
    </form>

    <script>
    $(document).ready(function () {
        $("#form-delete-lowongan").validate({
            rules: {},
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            if ($.fn.DataTable.isDataTable('#lowongan-table')) {
                                $('#lowongan-table').DataTable().ajax.reload(null, false);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message || 'Mohon cek kembali inputan Anda.'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada server.'
                        });
                    }
                });
                return false;
            }
        });
    });
    </script>
@endempty
