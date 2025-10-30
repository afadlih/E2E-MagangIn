@empty($periode)
    <div id="myModal" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data periode tidak ditemukan.
                </div>
                <a href="{{ url('/periode') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/periode/' . $periode->periode_id . '/delete_ajax') }}" method="POST" id="form-delete">
        @csrf
        @method('DELETE')
        <div class="modal-header">
            <h5 class="modal-title">Hapus Data periode</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <h5><i class="icon fas fa-ban"></i> Konfirmasi !!!</h5>
                Apakah Anda yakin ingin menghapus data periode berikut?
            </div>
            <table class="table table-sm table-bordered table-striped">
                <tr>
                    <th class="text-right col-3">Periode :</th>
                    <td class="col-9">{{ $periode->periode }}</td>
                </tr>
                <tr>
                    <th class="text-right col-3">Keterangan :</th>
                    <td class="col-9">{{ $periode->keterangan }}</td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-bs-dismiss="modal" aria-label="Batal">Batal</button>
            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        </div>
    </form>

    <script>
        $(document).ready(function () {
            $("#form-delete").validate({
                rules: {},
                submitHandler: function(form) {
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status) {
                                $('#myModal').modal('hide'); // Tutup modal

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });

                                // Reload DataTable
                                if ($.fn.DataTable.isDataTable('#periode-table')) {
                                    $('#periode-table').DataTable().ajax.reload(null, false);
                                }
                            } else {
                                $('.text-danger').text(''); // reset error text
                                if (response.msgField) {
                                    $.each(response.msgField, function(prefix, val) {
                                        $('#error-' + prefix).text(val[0]);
                                    });
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message || 'Mohon cek kembali inputan anda.'
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = 'Terjadi kesalahan pada server.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                if (xhr.responseJSON.message.includes('Integrity constraint violation')) {
                                    errorMsg = 'Data gagal dihapus karena masih digunakan pada data lain.';
                                } else {
                                    errorMsg = xhr.responseJSON.message;
                                }
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: errorMsg
                            });
                        }
                    });
                    return false; // prevent default submit
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
@endempty
