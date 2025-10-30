@empty($mahasiswa)
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
                <a href="{{ url('/mahasiswa') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/mahasiswa/' . $mahasiswa->mhs_nim . '/update_mhs') }}" method="POST" id="form-edit" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-header" style="background-color: #1a2e4f; color: white;">
            <h5 class="modal-title">Edit Data mahasiswa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

            {{-- Foto Profil Bulat --}}
            <div class="mb-2  text-center">
                @if ($mahasiswa->profile_picture)
                    <img id="preview-img" src="{{ asset('storage/' . $mahasiswa->profile_picture) }}" 
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


            {{-- Form input lain --}}
             <div class="form-group">
                <label>NIM</label>
                <input type="text" name="mhs_nim" id="mhs_nim" class="form-control" 
                       value="{{ $mahasiswa->mhs_nim }}" readonly>
                <small class="form-text text-muted">NIM tidak dapat diubah.</small>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="full_name" id="full_name" class="form-control" 
                       value="{{ $mahasiswa->full_name }}" required>
                <small id="error-full_name" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" id="alamat" class="form-control">{{ $mahasiswa->alamat }}</textarea>
                <small id="error-alamat" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>Telepon</label>
                <input type="text" name="telp" id="telp" class="form-control" 
                       value="{{ $mahasiswa->telp }}">
                <small id="error-telp" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>Status Magang</label>
                <input type="text" class="form-control" value="{{ ucfirst($mahasiswa->status_magang) }}" readonly>
            </div>

            <div class="form-group">
                <label>Prodi</label>
                <input type="text" class="form-control" 
                       value="{{ $mahasiswa->prodi->nama_prodi ?? '-' }}" readonly>
            </div>

            <div class="form-group">
                <label>Angkatan</label>
                <input type="text" name="angkatan" id="angkatan" class="form-control" 
                       value="{{ $mahasiswa->angkatan }}" required>
                <small id="error-angkatan" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
                    <option value="L" {{ $mahasiswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ $mahasiswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                <small id="error-jenis_kelamin" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>IPK</label>
                <input type="text" name="ipk" id="ipk" class="form-control" 
                       value="{{ $mahasiswa->ipk }}" required>
                <small id="error-ipk" class="error-text form-text text-danger"></small>
            </div>

 {{-- Bidang Keahlian (multi-checkbox seperti Skills) --}}
 <div class="form-group">
    <label>Bidang Keahlian</label>
    <div class="row">
        @foreach($bidangKeahlian as $keahlian)
            <div class="col-md-4">
                <div class="form-check">
                    <input 
                        class="form-check-input"
                        type="checkbox"
                        name="bidang_keahlian_id[]"
                        id="bidang_keahlian_{{ $keahlian->id }}"
                        value="{{ $keahlian->id }}"
                        {{ in_array($keahlian->id, old('bidang_keahlian_id', $mahasiswa->bidangKeahlian->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="bidang_keahlian_{{ $keahlian->id }}">
                        {{ $keahlian->nama }}
                    </label>
                </div>
            </div>
        @endforeach
    </div>
    <small id="error-bidang_keahlian_id" class="text-danger"></small>
</div>

            {{-- Skills (multi-checkbox) --}}
            <div class="form-group">
            <label>Skills</label>
            <div class="row">
                @foreach($allSkills as $skill)
                        <div class="col-md-4">
                            <div class="form-check">
                            <input 
                                class="form-check-input"
                                type="checkbox"
                                name="skills[]"
                                id="skill_{{ $skill->id }}"
                                value="{{ $skill->id }}"
                                {{ in_array($skill->id, $mahasiswa->skills->pluck('id')->toArray()) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="skill_{{ $skill->id }}">
                                {{ $skill->nama }}
                            </label>
                            </div>
                        </div>
                        @endforeach
            </div>
            <small id="error-skills" class="text-danger"></small>
            </div>

            {{-- Durasi Magang --}}
            <div class="form-group">
            <label>Durasi Magang (bulan)</label>
            <select name="durasi" class="form-control" required>
                <option value="">— Pilih Durasi —</option>
                <option value="3" {{ $mahasiswa->durasi == 3 ? 'selected' : '' }}>3 bulan</option>
                <option value="6" {{ $mahasiswa->durasi == 6 ? 'selected' : '' }}>6 bulan</option>
            </select>
            <small id="error-durasi" class="text-danger"></small>
            </div>

            <div class="form-group">
                <label>Tipe Bekerja</label>
                <select name="tipe_bekerja" id="tipe_bekerja" class="form-control">
                    <option value="">-- Pilih Tipe Bekerja --</option>
                    <option value="remote" {{ $mahasiswa->tipe_bekerja == 'remote' ? 'selected' : '' }}>Remote</option>
                    <option value="on_site" {{ $mahasiswa->tipe_bekerja == 'on_site' ? 'selected' : '' }}>On_site</option>
                    <option value="hybrid" {{ $mahasiswa->tipe_bekerja == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                </select>
                <small id="error-tipe_bekerja" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>File CV</label><br>
                @if ($mahasiswa->file_cv)
                    <a href="{{ asset('storage/' . $mahasiswa->file_cv) }}" target="_blank" class="btn btn-info btn-sm">
                        Lihat CV
                    </a>
                @else
                    <span class="text-muted">Tidak ada CV</span>
                @endif
            </div>
            
            <div class="form-group">
                <label>File CV</label>
                <input type="file" name="file_cv" id="file_cv" class="form-control-file">
                <small class="form-text text-muted">Abaikan jika tidak ingin mengubah file CV.</small>
                <small id="error-file_cv" class="error-text form-text text-danger"></small>
                @if ($mahasiswa->file_cv)
                    <a href="{{ asset('storage/' . $mahasiswa->file_cv) }}" target="_blank" class="btn btn-info btn-sm mt-2">
                        Lihat CV
                    </a>
                @else
                    <span class="text-muted mt-2">Tidak ada CV</span>
                @endif
            </div>
            <div class="form-group">
                <label>Negara (Preferensi Lokasi)</label>
                <select name="negara_id" id="negara_id" class="form-control">
                    <option value="">-- Pilih Negara --</option>
                    @foreach ($negaraList as $negara)
                        <option value="{{ $negara->id }}"
                            {{ old('negara_id', $mahasiswa->preferensiLokasi->negara_id ?? '') == $negara->id ? 'selected' : '' }}>
                            {{ $negara->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            

            <div class="form-group">
                <label>Provinsi (Preferensi Lokasi)</label>
                <select name="provinsi_id" id="provinsi_id" class="form-control">
                    <option value="">-- Pilih Provinsi --</option>
                    @foreach ($provinsiList as $provinsi)
                        <option value="{{ $provinsi->id }}"
                            {{ old('provinsi_id', $mahasiswa->preferensiLokasi->provinsi_id ?? '') == $provinsi->id ? 'selected' : '' }}>
                            {{ $provinsi->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label>Kabupaten (Preferensi Lokasi)</label>
                <select name="kabupaten_id" id="kabupaten_id" class="form-control">
                    <option value="">-- Pilih Kabupaten --</option>
                    @foreach ($kabupatenList as $kabupaten)
                        <option value="{{ $kabupaten->id }}"
                            {{ old('kabupaten_id', $mahasiswa->preferensiLokasi->kabupaten_id ?? '') == $kabupaten->id ? 'selected' : '' }}>
                            {{ $kabupaten->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label>Username</label>
                <input value="{{ $mahasiswa->user->username }}" type="text" name="username" id="username" class="form-control" required>
                <small id="error-username" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input value="" type="password" name="password" id="password" class="form-control">
                <small class="form-text text-muted">Abaikan jika tidak ingin ubah password</small>
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

    // Klik tombol Edit Profile untuk trigger input file
    $('#btn-edit-profile').click(function () {
        $('#profile_picture').click();
    });

    // Preview gambar saat pilih file baru
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

    // Tombol hapus foto profil dengan Ajax DELETE
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
                    url: "{{ route('mhs.hapus_foto', $mahasiswa->mhs_nim) }}",
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if(response.status) {
                            // Reset preview gambar ke default
                            $('#preview-img').attr('src', "{{ asset('img/user.png') }}");
                            // Kosongkan input file
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

    // Validasi dan submit ajax sama seperti sebelumnya...
    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    }, 'Ukuran file maksimal {0} byte.');

    $("#form-edit").validate({
        rules: {
            full_name: { required: true, maxlength: 100 },
            alamat: { maxlength: 255 },
            telp: { maxlength: 20 },
            prodi_id: { digits: true },
            angkatan: { required: true, digits: true, minlength: 4, maxlength: 4 },
            jenis_kelamin: { required: true, pattern: /^(L|P)$/ },
            ipk: { number: true, min: 0, max: 4 },
            bidang_keahlian: { maxlength: 100 },
            file_cv: { extension: "pdf|doc|docx" },
            provinsi_id: { digits: true },
            kabupaten_id: { digits: true },
            username: { required: true, maxlength: 20 },
            password: { minlength: 5, maxlength: 20 },
            profile_picture: { extension: "jpg|jpeg|png|webp", filesize: 2048000 } // max 2 MB
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
