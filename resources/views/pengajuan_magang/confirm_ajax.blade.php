@if (@empty($lamaran) || @empty($prodi) || @empty($perusahaan))
    <div id="myModal" class="modal-dialog modal-lg" role="document">
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
    <form action="{{ url('/pengajuan-magang/' . $lamaran->lamaran_id . '/delete_ajax') }}" method="POST" id="form-delete">
        @csrf
        @method('DELETE')
        <div class="modal-header" style="background-color: #1a2e4f; color: white;">
            <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Hapus Data Lamaran</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <h5 class="alert-heading">Konfirmasi!!!</h5>
                    <p>Apakah Anda yakin ingin menghapus data lamaran berikut?</p>
                </div>
            </div>

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
                            <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Program Studi:</th>
                            <td>{{ $prodi->nama_prodi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Status Lamaran:</th>
                            <td>
                                <span class="badge" style="{{ $lamaran->status == 'diterima' ? 'background-color: #28a745; color: white;' : ($lamaran->status == 'ditolak' ? 'background-color: #dc3545; color: white;' : 'background-color: #f4b740; color: #1a2e4f;') }}">
                                    {{ $lamaran->status }}
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
                            <th class="text-right" style="background-color: #f7f9fc; color: #1a2e4f;">Tanggal Lamaran:</th>
                            <td>{{ $lamaran->tanggal_lamaran ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Dosen Pembimbing --}}
            @if ($lamaran->dosen)
                <h6 style="color: #1a2e4f; font-weight: 600;"><i class="fas fa-chalkboard-teacher me-2"></i>Dosen Pembimbing</h6>
                <div class="card mb-4">
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered mb-0">
                            <tr>
                                <th class="text-right col-4" style="background-color: #f7f9fc; color: #1a2e4f;">Nama Dosen:</th>
                                <td>{{ $lamaran->dosen->nama ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning btn-sm" style="background-color: #f4b740; border-color: #f4b740; color: #1a2e4f;" data-bs-dismiss="modal" aria-label="Batal">
                <i class="fas fa-times me-2"></i>Batal
            </button>
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash me-2"></i>Ya, Hapus
            </button>
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
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Data lamaran berhasil dihapus.'
                                });
                                if ($.fn.DataTable.isDataTable('#pengajuan-magang-table')) {
                                    $('#pengajuan-magang-table').DataTable().ajax.reload(null, false);
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message || 'Mohon cek kembali.'
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = 'Terjadi kesalahan pada server.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                if (xhr.responseJSON.message.includes('Integrity constraint violation')) {
                                    errorMsg = 'Data gagal dihapus karena masih digunakan pada data lain.';
                                } else {
                                    errorMsg = xhr.responseJSON.message;
                                }
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: errorMsg
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