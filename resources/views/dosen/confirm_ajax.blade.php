@empty($dosen)
    <div id="myModal" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
           <div class="modal-header" style="background-color: #1a2e4f; color: white;">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data dosen tidak ditemukan.
                </div>
                <a href="{{ url('/dosen') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/dosen/' . $dosen->dosen_id . '/delete_ajax') }}" method="POST" id="form-delete">
        
        @csrf
        @method('DELETE')
        <div class="modal-header" style="background-color: #1a2e4f; color: white;">
            <h5 class="modal-title">Hapus Data Dosen</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <h5><i class="icon fas fa-ban"></i> Konfirmasi !!!</h5>
                Apakah Anda yakin ingin menghapus data dosen berikut?
            </div>
            <table class="table table-sm table-bordered table-striped">
                <tr>
                    <th class="text-right col-3">ID Dosen :</th>
                    <td class="col-9">{{ $dosen->dosen_id }}</td>
                </tr>
                <tr>
                    <th class="text-right col-3">Nama Dosen :</th>
                    <td class="col-9">{{ $dosen->nama }}</td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
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
                        if(response.status) {
                            $('#myModal').modal('hide'); // Tutup modal

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });

                            // Reload DataTable untuk dosen
                            if ($.fn.DataTable.isDataTable('#dosen-table')) {
                                $('#dosen-table').DataTable().ajax.reload(null, false);
                            }
                        } else {
                            $('.text-danger').text(''); // Reset pesan error
                            if(response.msgField){
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message || 'Mohon cek kembali inputan Anda.'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada server.'
                        });
                    }
                });
                return false; // Cegah submit default
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
