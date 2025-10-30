<form action="{{ url('/perusahaan-mitra/ajax') }}" method="POST" id="form-tambah-perusahaan" autocomplete="off">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title">Tambah Data Perusahaan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="nama" class="form-label">Nama Perusahaan</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
            <div class="text-danger" id="error-nama"></div>
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email">
            <div class="text-danger" id="error-email"></div>
        </div>

        <div class="form-group">
            <label for="telp" class="form-label">Telepon</label>
            <input type="text" class="form-control" id="telp" name="telp">
            <div class="text-danger" id="error-telp"></div>
        </div>

        <div class="form-group">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
            <div class="text-danger" id="error-alamat"></div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $("#form-tambah-perusahaan").validate({
        rules: {
            nama: { required: true, minlength: 3, maxlength: 100 },
            email: { email: true },
            telp: { maxlength: 20 },
            alamat: { maxlength: 255 }
        },
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

                        if ($.fn.DataTable.isDataTable('#perusahaan-table')) {
                            $('#perusahaan-table').DataTable().ajax.reload(null, false);
                        }
                    } else {
                        $('.text-danger').text('');
                        if (response.msgField) {
                            $.each(response.msgField, function(field, messages) {
                                $('#error-' + field).text(messages[0]);
                            });
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Periksa kembali inputan Anda.'
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
