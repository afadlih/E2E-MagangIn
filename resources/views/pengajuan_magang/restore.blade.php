<form action="{{ route('pengajuan-magang.restore', $lamaran->lamaran_id) }}" method="POST" id="form-restore">
    @csrf
    <div class="modal-header" style="background-color: #1a2e4f; color: white;">
        <h5 class="modal-title"><i class="fas fa-undo me-2"></i>Pulihkan Data Lamaran</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-info d-flex align-items-center">
            <i class="fas fa-info-circle fa-2x me-3"></i>
            <div>
                <h5 class="alert-heading">Konfirmasi!!!</h5>
                <p>Apakah Anda yakin ingin memulihkan data lamaran berikut?</p>
            </div>
        </div>
        <table class="table table-sm table-bordered mb-0">
            <tr>
                <th class="text-right col-4" style="background-color: #f7f9fc; color: #1a2e4f;">NIM:</th>
                <td>{{ $lamaran->mahasiswa->mhs_nim }}</td>
            </tr>
            <tr>
                <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Nama Lengkap:</th>
                <td>{{ $lamaran->mahasiswa->full_name }}</td>
            </tr>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-warning btn-sm" style="background-color: #f4b740; border-color: #f4b740; color: #1a2e4f;" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Batal
        </button>
        <button type="submit" class="btn btn-success btn-sm">
            <i class="fas fa-undo me-2"></i>Ya, Pulihkan
        </button>
    </div>
</form>

<script>
$(document).ready(function () {
    $("#form-restore").validate({
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
                        if ($.fn.DataTable.isDataTable('#pengajuan-magang-table')) {
                            $('#pengajuan-magang-table').DataTable().ajax.reload(null, false);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message || 'Mohon cek kembali.'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Terjadi kesalahan pada server.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errorMsg
                    });
                }
            });
            return false;
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        }
    });
});
</script>