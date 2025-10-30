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
                    Data mahasiswa tidak ditemukan.
                </div>
                <a href="{{ url('/mahasiswa') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/mahasiswa/' . $mahasiswa->mhs_nim . '/delete_ajax') }}" method="POST" id="form-delete">
        @csrf
        @method('DELETE')
        <div class="modal-header">
            <h5 class="modal-title">Hapus Data Mahasiswa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <h5><i class="icon fas fa-ban"></i> Konfirmasi !!!</h5>
                Apakah Anda yakin ingin menghapus data mahasiswa berikut?
            </div>
            <table class="table table-sm table-bordered table-striped">
                <tr>
                    <th class="text-right col-3">NIM :</th>
                    <td class="col-9">{{ $mahasiswa->mhs_nim }}</td>
                </tr>
                <tr>
                    <th class="text-right col-3">Nama Lengkap :</th>
                    <td class="col-9">{{ $mahasiswa->full_name }}</td>
                </tr>
                <tr>
                    <th class="text-right col-3">Program Studi :</th>
                    <td class="col-9">{{ $mahasiswa->prodi->nama_prodi ?? '-' }}</td>
                </tr>

                <tr>
                    <th class="text-right col-3">Angkatan :</th>
                    <td class="col-9">{{ $mahasiswa->angkatan }}</td>
                </tr>
                <tr>
                    <th class="text-right col-3">Jenis Kelamin :</th>
                    <td class="col-9">{{ $mahasiswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <th class="text-right col-3">IPK :</th>
                    <td class="col-9">{{ $mahasiswa->ipk ?? '-' }}</td>
                </tr>
                <tr>
                <th class="text-right">Bidang Keahlian:</th>
                <td>
                    @if ($mahasiswa->bidangKeahlian->isNotEmpty())
                        <ul class="mb-0 pl-3">
                            @foreach ($mahasiswa->bidangKeahlian as $minat)
                                <li>{{ $minat->nama }}</li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-muted">Tidak ada minat terdaftar</span>
                    @endif
                </td>
            </tr>

            <tr>
                <th class="text-right">Skills:</th>
                <td>
                    @if ($mahasiswa->skills->isNotEmpty())
                        <ul class="mb-0 pl-3">
                            @foreach ($mahasiswa->skills as $skill)
                                <li>{{ $skill->nama }}</li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-muted">Tidak ada skill terdaftar</span>
                    @endif
                </td>
            </tr>
            
            <tr>
                <th class="text-right">File CV:</th>
                <td>
                    @if ($mahasiswa->file_cv)
                        <a href="{{ asset('storage/' . $mahasiswa->file_cv) }}" target="_blank" class="btn btn-info btn-sm">
                            Lihat CV
                        </a>
                    @else
                        <span class="text-muted">Tidak ada CV</span>
                    @endif
                </td>
            </tr>

            <tr>
                <th class="text-right">Preferensi Lokasi:</th>
                <td>
                    @if ($mahasiswa->preferensiLokasi)
                        {{ $mahasiswa->preferensiLokasi->kabupaten->nama ?? '-' }},
                        {{ $mahasiswa->preferensiLokasi->provinsi->nama ?? '-' }},
                        {{ $mahasiswa->preferensiLokasi->negara->nama ?? '-' }}<br>
                    @else
                        <span class="text-muted">Belum diisi</span>
                    @endif
                </td>
            </tr>

            <tr>
                <th class="text-right">Durasi Magang:</th>
                <td>{{ $mahasiswa->durasi}} bulan</td>
            </tr>

            <tr>
                <th class="text-right">Tipe Bekerja:</th>
                <td>{{$mahasiswa->tipe_bekerja}}</td>
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
