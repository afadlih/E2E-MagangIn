@empty($mahasiswa)
    <div id="modal-delete" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data mahasiswa tidak ditemukan
                </div>
                <a href="{{ url('/mahasiswa') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div class="modal-header">
        <h5 class="modal-title">Detail Mahasiswa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
    </div>
    <div class="modal-body">
        <table class="table table-sm table-bordered table-striped">
            <tr>
                <th class="text-right col-4">NIM:</th>
                <td class="col-8">{{ $mahasiswa->mhs_nim }}</td>
            </tr>
            <tr>
                <th class="text-right">Nama Lengkap:</th>
                <td>{{ $mahasiswa->full_name }}</td>
            </tr>
            <tr>
                <th class="text-right">Alamat:</th>
                <td>{{ $mahasiswa->alamat }}</td>
            </tr>
            <tr>
                <th class="text-right">No. Telepon:</th>
                <td>{{ $mahasiswa->telp }}</td>
            </tr>
            <tr>
                <th class="text-right">Program Studi:</th>
                <td class="col-9">{{ $mahasiswa->prodi->nama_prodi ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-right">Angkatan:</th>
                <td>{{ $mahasiswa->angkatan }}</td>
            </tr>
            <tr>
                <th class="text-right">Jenis Kelamin:</th>
                <td>{{ $mahasiswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <th class="text-right">IPK:</th>
                <td>{{ $mahasiswa->ipk ?? '-' }}</td>
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
                <th class="text-right">Skills</th>
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
                <td>
                    @if ($mahasiswa->durasi)
                        {{ $mahasiswa->durasi }} bulan
                    @else
                        <span class="text-muted">Belum diisi</span>
                    @endif
                </td>
            </tr>
            
            <tr>
                <th class="text-right">Status Magang:</th>
                <td>{{ ucfirst($mahasiswa->status_magang) }}</td>
            </tr>
            <tr>
                <th class="text-right">Username:</th>
                <td>{{ $mahasiswa->user->username ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-right">Password:</th>
                <td class="col-9">********</td>
            </tr>
        </table>
    </div>
    <div class="modal-footer">
        <button onclick="modalAction('{{ url('/mahasiswa/' . $mahasiswa->mhs_nim . '/edit_ajax') }}')" class="btn btn-success btn-sm">
            Edit
        </button>
        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" aria-label="Close">
            Close
        </button>
    </div>
@endempty
