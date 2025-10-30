<form action="{{ route('register.mahasiswa') }}" method="POST" id="form-tambah" autocomplete="off">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title">Register Mahasiswa</h5>
    </div>
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

        <input type="hidden" name="level_id" value="3">

        {{-- DATA MAHASISWA --}}
        <div class="form-group">
            <label for="mhs_nim" class="form-label">NIM</label>
            <input type="text" class="form-control" id="mhs_nim" name="mhs_nim" required>
            <div class="text-danger" id="error-mhs_nim"></div>
        </div>

        <div class="form-group">
            <label for="full_name" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="full_name" name="full_name" required>
            <div class="text-danger" id="error-full_name"></div>
        </div>

        <div class="form-group">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" id="alamat" name="alamat"></textarea>
            <div class="text-danger" id="error-alamat"></div>
        </div>

        <div class="form-group">
            <label for="telp" class="form-label">Telepon</label>
            <input type="text" class="form-control" id="telp" name="telp">
            <div class="text-danger" id="error-telp"></div>
        </div>

        <div class="form-group">
            <label for="prodi_id" class="form-label">Program Studi</label>
            <select name="prodi_id" id="prodi_id" class="form-select" required>
                <option value="">-- Pilih Program Studi --</option>
                @foreach($prodis as $prodi)
                    <option value="{{ $prodi->prodi_id }}">{{ $prodi->nama_prodi }}</option>
                @endforeach
            </select>
            <div class="text-danger" id="error-prodi_id"></div>
        </div>

        <div class="form-group">
            <label for="angkatan" class="form-label">Angkatan</label>
            <input type="text" class="form-control" id="angkatan" name="angkatan" required>
            <div class="text-danger" id="error-angkatan"></div>
        </div>

        <div class="form-group">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
            <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
            <div class="text-danger" id="error-jenis_kelamin"></div>
        </div>

        <div class="form-group">
            <label for="ipk" class="form-label">IPK</label>
            <input type="text" class="form-control" id="ipk" name="ipk">
            <div class="text-danger" id="error-ipk"></div>
        </div>

        <input type="hidden" name="status_magang" value="belum magang">
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-warning btn-batal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $("#form-tambah").validate({
        rules: {
            level_id: { required: true, number: true },
            username: { required: true, minlength: 3, maxlength: 20 },
            password: { required: true, minlength: 5, maxlength: 20 },
            mhs_nim: { required: true, minlength: 3, maxlength: 20 },
            full_name: { required: true, minlength: 3, maxlength: 100 },
            prodi_id: { required: true }
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
                        if ($.fn.DataTable.isDataTable('#mahasiswa-table')) {
                            $('#mahasiswa-table').DataTable().ajax.reload(null, false);
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
                error: function(xhr) {
                    if (xhr.status === 422) {
                        $('.text-danger').text('');
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#error-' + key).text(value[0]);
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: xhr.responseJSON.message || 'Mohon periksa kembali input Anda.'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada server.'
                        });
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
    $(document).on('click', '.btn-batal', function () {
            $('#myModal').modal('hide');
        });
    });

</script>
