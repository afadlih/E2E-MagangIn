@empty($dosen)
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
                <a href="{{ url('/dosen') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/dosen/' . $dosen->dosen_id . '/update_ajax') }}" method="POST" id="form-edit">
        @csrf
        @method('PUT')
        <div class="modal-header" style="background-color: #1a2e4f; color: white;">
            <h5 class="modal-title">Edit Data Dosen</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" id="nama" class="form-control" 
                       value="{{ $dosen->nama }}" required>
                <small id="error-nama" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="email" class="form-control" 
                       value="{{ $dosen->email }}" required>
                <small id="error-email" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="telp" id="telp" class="form-control" 
                       value="{{ $dosen->telp }}">
                <small id="error-telp" class="error-text form-text text-danger"></small>
            </div>
            
            <div class="form-group text-left">
                <label for="id_minat">Bidang Penelitian</label>
                <select name="id_minat" class="form-control">
                    <option value="">-- Pilih Bidang Penelitian --</option>
                    @foreach($bidang_penelitian as $bidang)
                        <option value="{{ $bidang->id_minat }}"
                            {{ (old('id_minat', $dosen->id_minat) == $bidang->id_minat) ? 'selected' : '' }}>
                            {{ $bidang->bidang }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="form-group">
                <label>Username</label>
                <input value="{{ $dosen->user->username ?? '' }}" type="text" name="username" id="username" class="form-control" required>
                <small id="error-username" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input value="" type="password" name="password" id="password" class="form-control">
                <small class="form-text text-muted">Abaikan jika tidak ingin mengubah password</small>
                <small id="error-password" class="error-text form-text text-danger"></small>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-bs-dismiss="modal" aria-label="Batal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>

    <script>
    $(document).ready(function () {
        $("#form-edit").validate({
            rules: {
            username: { required: true, minlength: 3, maxlength: 20 },
            password: { minlength: 5, maxlength: 20 },
            nama: { required: true, minlength: 3, maxlength: 100 },
            email: { required: true, email: true },
            telp: { required: true, maxlength: 20 },
            id_minat: { required: true }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if(response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            if ($.fn.DataTable.isDataTable('#dosen-table')) {
                                $('#dosen-table').DataTable().ajax.reload(null, false);
                            }
                        } else {
                            $('.text-danger').text('');
                            if(response.msgField){
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
@endempty
