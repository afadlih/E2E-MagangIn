@empty($notifikasi)
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
                    Data notifikasi tidak ditemukan
                </div>
                <a href="{{ url('/message') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div class="modal-header">
        <h5 class="modal-title">Detail Notifikasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
    </div>
    <div class="modal-body">
        <table class="table table-sm table-bordered table-striped">
            <tr>
                <th class="text-right">Judul:</th>
                <td>{{ $notifikasi->judul }}</td>
            </tr>
            <tr>
                <th class="text-right">Pesan:</th>
                <td>{!! nl2br(e($notifikasi->pesan)) !!}</td>
            </tr>
            <tr>
                <th class="text-right">Waktu Dibuat:</th>
                <td>{{ $notifikasi->waktu_dibuat }}</td>
            </tr>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" aria-label="Close">
            Close
        </button>
    </div>
@endempty
