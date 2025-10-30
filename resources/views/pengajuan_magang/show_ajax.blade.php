@if (@empty($lamaran) || @empty($prodi) || @empty($perusahaan))
    <div id="modal-delete" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #1a2e4f; color: white;">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Kesalahan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="fas fa-ban fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading">Kesalahan!!!</h5>
                        <p>Data lamaran, lowongan, atau prodi tidak ditemukan</p>
                    </div>
                </div>
                <a href="{{ url('/pengajuan-magang') }}" class="btn btn-warning btn-sm" style="background-color: #f4b740; border-color: #f4b740; color: #1a2e4f;"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
            </div>
        </div>
    </div>
@else
    <div class="modal-header" style="background-color: #1a2e4f; color: white;">
        <h5 class="modal-title"><i class="fas fa-file-alt me-2"></i>Detail Lamaran Mahasiswa</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        {{-- Informasi Mahasiswa --}}
        <h6 style="color: #1a2e4f; font-weight: 600;"><i class="fas fa-user-graduate me-2"></i>Informasi Mahasiswa</h6>
        <div class="card mb-4">
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <tr>
                        <th class="text-right col-4" style="background-color: #f7f9fc; color: #1a2e4f;">NIM:</th>
                        <td class="col-8">{{ $lamaran->mahasiswa->mhs_nim }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Nama Lengkap:</th>
                        <td>{{ $lamaran->mahasiswa->full_name }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Alamat:</th>
                        <td>{{ $lamaran->mahasiswa->alamat }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">No. Telepon:</th>
                        <td>{{ $lamaran->mahasiswa->telp }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Program Studi:</th>
                        <td>{{ $prodi->nama_prodi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Status Lamaran:</th>
                        <td>
                            @php
                                $status = $lamaran->status;
                                $style = match($status) {
                                    'diterima' => 'background-color: #28a745; color: white;',
                                    'ditolak' => 'background-color: #dc3545; color: white;',
                                    'selesai' => 'background-color: #6C63FF; color: white;',
                                    default => 'background-color: #f4b740; color: #1a2e4f;',
                                };
                            @endphp

                            <span class="badge" style="{{ $style }}">
                                {{ $status }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Informasi Lowongan --}}
        <h6 style="color: #1a2e4f; font-weight: 600;"><i class="fas fa-briefcase me-2"></i>Informasi Lowongan</h6>
        <div class="card mb-4">
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <tr>
                        <th class="text-right col-4" style="background-color: #f7f9fc; color: #1a2e4f;">Judul Lowongan:</th>
                        <td>{{ $lamaran->lowongan->judul ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Perusahaan:</th>
                        <td>{{ $perusahaan->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Lokasi:</th>
                        <td>{{ $perusahaan->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Tanggal Lamaran:</th>
                        <td>{{ $lamaran->tanggal_lamaran ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Dosen Pembimbing --}}
        @if ($lamaran->dosen && $lamaran->status != 'ditolak')
            <h6 style="color: #1a2e4f; font-weight: 600;"><i class="fas fa-chalkboard-teacher me-2"></i>Dosen Pembimbing</h6>
            <div class="card mb-4">
                <div class="card-body p-0">
                    <table class="table table-sm table-bordered mb-0">
                        <tr>
                            <th class="text-right col-4" style="background-color: #f7f9fc; color: #1a2e4f;">Nama Dosen:</th>
                            <td>{{ $lamaran->dosen->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Email Dosen:</th>
                            <td>{{ $lamaran->dosen->email ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @elseif ($lamaran->status == 'ditolak')
            {{-- tidak menampilkan dosen jika status telah ditolak --}}
        @else
            <h6 style="color: #1a2e4f; font-weight: 600;"><i class="fas fa-chalkboard-teacher me-2"></i>Pilih Dosen Pembimbing</h6>
            <div class="card mb-4">
                <div class="card-body p-0">
                    <table class="table table-sm table-bordered mb-0">
                        <tr>
                            <th class="text-right col-4" style="background-color: #f7f9fc; color: #1a2e4f;">Nama Dosen:</th>
                            <td>
                                <select name="dosen_id" class="form-select form-select-sm">
                                    <option value="">-- Pilih Dosen --</option>
                                    @foreach ($dosens as $dosen)
                                        <option value="{{ $dosen->dosen_id }}" {{ $lamaran->dosen_id == $dosen->dosen_id ? 'selected' : '' }}>
                                            {{ $dosen->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif

        {{-- CV Mahasiswa --}}
        @if ($lamaran->mahasiswa->file_cv)
            <h6 style="color: #1a2e4f; font-weight: 600;"><i class="fas fa-file-pdf me-2"></i>Curriculum Vitae</h6>
            <div class="card mb-4">
                <div class="card-body p-0">
                    <iframe src="{{ asset('storage/' . $lamaran->mahasiswa->file_cv) }}" width="100%" height="500px" style="border: none;"></iframe>
                </div>
            </div>
        @endif
    </div>

    @if ($lamaran->status == 'ditolak' || $lamaran->status == 'diterima' || $lamaran->status == 'selesai')
        {{-- tidak menampilkan button jika ditolak atau diterima --}}
    @else
        <div class="modal-footer">
            <button onclick="submitLamaran('{{ $lamaran->lamaran_id }}', 'diterima')" class="btn btn-sm" style="background-color: #28a745; border-color: #28a745; color: white;">
                <i class="fas fa-check me-2"></i>Terima
            </button>
            <button onclick="submitLamaran('{{ $lamaran->lamaran_id }}', 'ditolak')" class="btn btn-sm" style="background-color: #dc3545; border-color: #dc3545; color: white;">
                <i class="fas fa-times me-2"></i>Tolak
            </button>

        </div>
    @endif
@endempty

<script>
    function submitLamaran(lamaranId, status) {
        let dosenId = $('select[name="dosen_id"]').val();
        let baseUrl = "{{ url('/') }}";

        if (status === 'diterima' && (!dosenId || dosenId === '')) {
            Swal.fire({
                icon: 'warning',
                title: 'Validasi Gagal',
                text: 'Pilih dosen pembimbing terlebih dahulu sebelum menerima lamaran.'
            });
            return;
        }

        $.ajax({
            url: baseUrl + '/pengajuan-magang/' + lamaranId + '/update_status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status,
                dosen_id: status === 'diterima' ? dosenId : null
            },
            success: function (response) {
                if (response.status) {
                    $('#myModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    });
                    if ($.fn.DataTable.isDataTable('#pengajuan-magang-table')) {
                        $('#pengajuan-magang-table').DataTable().ajax.reload(null, false);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message
                    });
                }
            },
            error: function (xhr) {
                let msg = xhr.responseJSON?.message || 'Terjadi kesalahan.';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg
                });
            }
        });
    }
</script>