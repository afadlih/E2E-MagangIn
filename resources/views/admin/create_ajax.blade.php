<form action="{{ url('/admin/ajax') }}" method="POST" id="form-tambah-admin" autocomplete="off">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title">Tambah Data Admin</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        {{-- USER DATA --}}
         <div class="modal-body">
            <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required autocomplete="off">
            <div class="text-danger" id="error-username"></div>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
            <div class="text-danger" id="error-password"></div>
        </div>

        <input type="hidden" name="level_id" value="1"> {{-- level_id untuk admin --}}

        {{-- ADMIN DATA --}}
        <div class="form-group">
            <label for="nama" class="form-label">Nama Lengkap</label>
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
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $("#form-tambah-admin").validate({
        rules: {
            username: { required: true, minlength: 3, maxlength: 20 },
            password: { required: true, minlength: 5, maxlength: 20 },
            nama: { required: true, minlength: 3, maxlength: 100 },
            email: { required: true, email: true },
            telp: { required: true, maxlength: 20 }
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

                        if ($.fn.DataTable.isDataTable('#admin-table')) {
                            $('#admin-table').DataTable().ajax.reload(null, false);
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