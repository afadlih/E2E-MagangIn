@empty($aktivitas->lamaran->mahasiswa)
<div id="modalAktivitas" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Kesalahan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                Data mahasiswa tidak ditemukan
            </div>
            <a href="{{ url('/log-aktivitas-mhs') }}" class="btn btn-warning">Kembali</a>
        </div>
    </div>
</div>
@else
    <div class="modal-header" style="background-color: #1a2e4f; color: white;">
        <h5 class="modal-title">Detail Log Aktivitas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
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
                <th class="text-right">Keterangan:</th>
                <td>{{ $aktivitas->keterangan }}</td>
            </tr>
            <tr>
                <th class="text-right">Waktu:</th>
                <td>{{ $aktivitas->waktu->format('d-m-Y') }}</td>
            </tr>
        </table>
        <hr>
        <h5>Feedback/Saran dari Dosen:</h5>
        <ul class="list-group mb-3">
            @forelse($komentar as $k)
            <li class="list-group-item">
                <div>
                    <strong>{{ optional($k->dosen)->nama }}</strong>
                    <small class="text-muted">({{ $k->created_at->format('d-m-Y H:i') }})</small>
                    <p class="mb-0 mt-1">{{ $k->komentar }}</p>
                </div>
            </li>
            @empty
            <li class="list-group-item">Belum ada Feedback/Saran dari Dosen.</li>
            @endforelse
        </ul>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Kembali</button>
        </div>
    </div>
@endempty

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>