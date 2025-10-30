@empty($aktivitas->lamaran->mahasiswa)
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
                Data mahasiswa tidak ditemukan
            </div>
            <a href="{{ url('/log-aktivitas') }}" class="btn btn-warning">Kembali</a>
        </div>
    </div>
</div>
@else
<div class="modal-header" style="background-color: #1a2e4f; color: white;">
    <h5 class="modal-title">Detail Log Aktivitas</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    </button>
</div>
<div class="modal-body">
    <table class="table table-sm table-bordered table-striped">
        <tr>
            <th class="text-right col-4">Nama:</th>
            <td class="col-8">{{ optional($aktivitas->lamaran->mahasiswa)->full_name }}</td>
        </tr>
        <tr>
            <th class="text-right">Prodi:</th>
            <td>{{ optional(optional($aktivitas->lamaran->mahasiswa)->prodi)->nama_prodi }}</td>
        </tr>
        <tr>
            <th class="text-right">Judul Magang:</th>
            <td>{{ optional($aktivitas->lamaran->lowongan)->judul }}</td>
        </tr>
        <tr>
            <th class="text-right">Perusahaan:</th>
            <td>{{ optional(optional($aktivitas->lamaran->lowongan)->perusahaan)->nama }}</td>
        </tr>

        <tr>
            <th class="text-right">Keterangan:</th>
            <td>{{ $aktivitas->keterangan }}</td>
        </tr>
        <tr>
            <th class="text-right">Waktu:</th>
            <td>{{ $aktivitas->waktu->format('d-m-Y') }}</td>
        </tr>
    </table>

    <hr>
    <h5>Feedback/Saran:</h5>
    <ul class="list-group mb-3">
        @forelse($komentar as $k)
    <li class="list-group-item">
        <div>
            <strong>{{ optional($k->dosen)->nama }}</strong>
            <small class="text-muted">({{ $k->created_at }})</small>
            <p class="mb-0 mt-1">{{ $k->komentar }}</p>
        </div>
    </li>
        @empty
        <li class="list-group-item">Belum ada Feedback/Saran.</li>
        @endforelse
    </ul>



    <form id="form-komentar" data-id="{{ $aktivitas->aktivitas_id }}">
        <div class="form-group">
            <label for="komentar">Tulis Feedback/Saran:</label>
            <textarea name="komentar" id="komentar" class="form-control" required></textarea>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Kembali</button>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </div>
    </form>
</div>
@endempty

<div class="modal fade" id="modalAktivitas" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal content from your view -->
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    $('#form-komentar').submit(function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let komentar = $('#komentar').val();

        $.post(`{{ url('/log-aktivitas') }}/${id}/komentar`, {
            komentar: komentar,
            _token: '{{ csrf_token() }}'
        }).done(function(res) {
            // Tutup modal
            $('#myModal').modal('hide');

            // Tampilkan notifikasi
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: res.message,
                confirmButtonText: 'OK'
            });

            // Reload DataTable (ubah ID sesuai kebutuhan)
            if ($.fn.DataTable.isDataTable('#log-table')) {
                $('#log-table').DataTable().ajax.reload(null, false);
            }

        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Feedback/Saran gagal ditambahkan.',
                confirmButtonText: 'OK'
            });
        });
    });
});
</script>



