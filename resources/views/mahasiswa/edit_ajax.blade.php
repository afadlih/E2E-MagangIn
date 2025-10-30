@empty($mahasiswa)
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
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/mahasiswa') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/mahasiswa/' . $mahasiswa->mhs_nim . '/update_ajax') }}" method="POST" id="form-edit">
        @csrf
        @method('PUT')
        <div class="modal-header">
            <h5 class="modal-title">Edit Data Mahasiswa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
        </div>
        <div class="modal-body">

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
                       value="{{ $mahasiswa->ipk }}" placeholder="Masukkan IPK (jika ada)">
                <small id="error-ipk" class="error-text form-text text-danger"></small>
            </div>

            <div class="form-group">
                <label>Bidang Keahlian</label>
                <div class="row">
                    @foreach($bidangKeahlian as $keahlian)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input 
                                    class="form-check-input"
                                    type="checkbox"
                                    id="bidang_keahlian_{{ $keahlian->id }}"
                                    value="{{ $keahlian->id }}"
                                    {{ in_array($keahlian->id, old('bidang_keahlian_id', $mahasiswa->bidangKeahlian->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                                    disabled
                                >
                                <label class="form-check-label" for="bidang_keahlian_{{ $keahlian->id }}">
                                    {{ $keahlian->nama }}
                                </label>
                                {{-- Hidden input to preserve value if form is submitted --}}
                                @if(in_array($keahlian->id, old('bidang_keahlian_id', $mahasiswa->bidangKeahlian->pluck('id')->toArray() ?? [])))
                                    <input type="hidden" name="bidang_keahlian_id[]" value="{{ $keahlian->id }}">
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <small id="error-bidang_keahlian_id" class="text-danger"></small>
            </div>

            {{-- Skills (read-only checkbox) --}}
        <div class="form-group">
            <label>Skills</label>
            <div class="row">
                @foreach($allSkills as $skill)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input 
                                class="form-check-input"
                                type="checkbox"
                                id="skill_{{ $skill->id }}"
                                value="{{ $skill->id }}"
                                {{ in_array($skill->id, $mahasiswa->skills->pluck('id')->toArray()) ? 'checked' : '' }}
                                disabled
                            >
                            <label class="form-check-label" for="skill_{{ $skill->id }}">
                                {{ $skill->nama }}
                            </label>

                            {{-- Hidden input to preserve value if form is submitted --}}
                            @if(in_array($skill->id, $mahasiswa->skills->pluck('id')->toArray()))
                                <input type="hidden" name="skills[]" value="{{ $skill->id }}">
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Durasi Magang (read-only select) --}}
        <div class="form-group">
            <label>Durasi Magang (bulan)</label>
            <select class="form-control" disabled>
                <option value="">— Pilih Durasi —</option>
                <option value="3" {{ $mahasiswa->durasi == 3 ? 'selected' : '' }}>3 bulan</option>
                <option value="6" {{ $mahasiswa->durasi == 6 ? 'selected' : '' }}>6 bulan</option>
            </select>

            {{-- Hidden input to preserve selected value --}}
            <input type="hidden" name="durasi" value="{{ $mahasiswa->durasi }}">
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
                <label>Negara (Preferensi Lokasi)</label>
                <select class="form-control" disabled>
                    <option value="">-- Pilih Negara --</option>
                    @foreach ($negaraList as $negara)
                        <option value="{{ $negara->id }}"
                            {{ ($mahasiswa->preferensiLokasi->negara_id ?? '') == $negara->id ? 'selected' : '' }}>
                            {{ $negara->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Provinsi (Preferensi Lokasi)</label>
                <select class="form-control" disabled>
                    <option value="">-- Pilih Provinsi --</option>
                    @foreach ($provinsiList as $provinsi)
                        <option value="{{ $provinsi->id }}"
                            {{ ($mahasiswa->preferensiLokasi->provinsi_id ?? '') == $provinsi->id ? 'selected' : '' }}>
                            {{ $provinsi->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Kabupaten (Preferensi Lokasi)</label>
                <select class="form-control" disabled>
                    <option value="">-- Pilih Kabupaten --</option>
                    @foreach ($kabupatenList as $kabupaten)
                        <option value="{{ $kabupaten->id }}"
                            {{ ($mahasiswa->preferensiLokasi->kabupaten_id ?? '') == $kabupaten->id ? 'selected' : '' }}>
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
    $("#form-edit").validate({
        rules: {
            username: {required: true, maxlength: 20},
            password: {
                minlength: 5,
                maxlength: 20
            },
            full_name: {
                required: true,
                maxlength: 100
            },
            alamat: {
                maxlength: 255
            },
            telp: {
                maxlength: 20
            },
            prodi_id: {
                digits: true
            },
            angkatan: {
                required: true,
                digits: true,
                minlength: 4,
                maxlength: 4
            },
            jenis_kelamin: {
                required: true,
                pattern: /^(L|P)$/
            },
            ipk: {
                number: true,
                min: 0,
                max: 4
            }
        },
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

                        // Reload DataTable
                        if ($.fn.DataTable.isDataTable('#mahasiswa-table')) {
                            $('#mahasiswa-table').DataTable().ajax.reload(null, false);
                        }
                    } else {
                        $('.text-danger').text(''); // reset error text
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
