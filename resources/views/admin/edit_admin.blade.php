@empty($admin)
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
                <a href="{{ url('/admin') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/admin/' . $admin->admin_id . '/update_admin') }}" method="POST" id="form-edit-admin">
        @csrf
        @method('PUT')
        <div class="modal-header">
            <h5 class="modal-title">Edit Data Admin</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-2  text-center">
                @if ($admin->profile_picture)
                    <img id="preview-img" src="{{ asset('storage/' . $admin->profile_picture) }}" 
                         alt="Foto Profil" 
                         class="img-thumbnail rounded-circle" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <img id="preview-img" src="{{ asset('img/user.png') }}" 
                         alt="Foto Profil Default" 
                         class="img-thumbnail rounded-circle" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                @endif
            </div>

            {{-- Input file tersembunyi --}}
            <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*">

            {{-- Tombol Edit dan Hapus --}}
            <div class="mb-4 text-center">
                <button type="button" id="btn-edit-profile" class="btn btn-sm btn-primary mr-2">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
                <button type="button" id="btn-delete-profile" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash-alt"></i> Hapus Profile
                </button>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" id="nama" class="form-control" 
                       value="{{ $admin->nama }}" required>
                <small id="error-nama" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="email" class="form-control" 
                       value="{{ $admin->email }}" required>
                <small id="error-email" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="telp" id="telp" class="form-control" 
                       value="{{ $admin->telp }}">
                <small id="error-telp" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input value="{{ $admin->user->username ?? '' }}" type="text" name="username" id="username" class="form-control" required>
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

        // Tombol trigger untuk input file
        $('#btn-edit-profile').click(function () {
            $('#profile_picture').click();
        });

        // Preview gambar saat memilih file
        $('#profile_picture').change(function () {
            const input = this;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#preview-img').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        });

        // Hapus foto profil admin
        $('#btn-delete-profile').click(function () {
            Swal.fire({
                title: 'Yakin ingin menghapus foto profil?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.hapus_foto', $admin->admin_id) }}",
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status) {
                                $('#preview-img').attr('src', "{{ asset('img/user.png') }}");
                                $('#profile_picture').val('');
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Gagal menghapus foto profil.'
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
                }
            });
        });

        // Custom method untuk validasi ukuran file
        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param);
        }, 'Ukuran file maksimal {0} byte.');

        // Validasi dan submit form edit admin
        $("#form-edit-admin").validate({
            rules: {
                nama: { required: true, maxlength: 100 },
                email: { required: true, email: true },
                telp: { required: true, maxlength: 20 },
                username: { required: true, maxlength: 20 },
                password: { minlength: 5, maxlength: 20 },
                profile_picture: { extension: "jpg|jpeg|png|webp", filesize: 2048000 } // max 2MB
            },
            messages: {
                profile_picture: {
                    extension: "Format file harus jpg, jpeg, png, atau webp.",
                    filesize: "Ukuran file maksimal 2 MB."
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: new FormData(form),
                    processData: false,
                    contentType: false,
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
                                $.each(response.msgField, function (prefix, val) {
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
                    error: function () {
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
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            }
        });

    });
</script>

@endempty
