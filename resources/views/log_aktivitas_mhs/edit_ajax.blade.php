@empty($aktivitas)
    <div id="myModal" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/log-aktivitas-mhs') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/log-aktivitas-mhs/' . $aktivitas->aktivitas_id . '/update_ajax') }}" method="POST" id="form-edit-aktivitas" autocomplete="off">
        @csrf
        @method('PUT')
        <div class="modal-header">
            <h5 class="modal-title">Edit Log Aktivitas Mahasiswa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="lamaran_id" value="{{ $aktivitas->lamaran_id }}">
            <input type="hidden" name="waktu" value="{{ $aktivitas->waktu }}">

            <div class="form-group">
                <label for="keterangan" class="form-label">Keterangan</label>
                <input type="text" class="form-control" id="keterangan" name="keterangan" value="{{ $aktivitas->keterangan }}" required>
                <div class="text-danger" id="error-keterangan"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-bs-dismiss="modal" aria-label="Batal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>

    <script>
    $(document).ready(function() {
        $("#form-edit-aktivitas").validate({
            rules: {
                keterangan: { required: true, maxlength: 255 },
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if(response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message });
                            if ($.fn.DataTable.isDataTable('#log-table')) {
                                $('#log-table').DataTable().ajax.reload(null, false);
                            }
                        } else {
                            $('.text-danger').text('');
                            if(response.msgField){
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                            }
                            Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: response.message || 'Mohon cek kembali inputan anda.' });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON) {
                            const response = xhr.responseJSON;
                            $('.text-danger').text('');
                            if(response.msgField){
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                            }
                            Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: response.message || 'Mohon cek kembali inputan anda.' });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan pada server.' });
                        }
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
@endempty
