<div class="modal-header">
    <h5 class="modal-title">Detail Pengajuan Magang</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#myModal').modal('hide')">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label><strong>NIM Mahasiswa:</strong></label>
                <p>{{ $pengajuan->mhs_nim ?? '-' }}</p>
            </div>
            
            <div class="form-group">
                <label><strong>Nama Dosen Pembimbing:</strong></label>
                <p>{{ $pengajuan->dosen_nama ?? 'Belum ditentukan' }}</p>
            </div>
            
            <div class="form-group">
                <label><strong>Tanggal Lamaran:</strong></label>
                <p>{{ \Carbon\Carbon::parse($pengajuan->tanggal_lamaran)->format('d F Y H:i') }}</p>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label><strong>Lowongan Magang:</strong></label>
                <p>{{ $pengajuan->lowongan_nama ?? '-' }}</p>
            </div>
            
            <div class="form-group">
                <label><strong>Status:</strong></label>
                <p>
                    @switch($pengajuan->status)
                        @case('diterima')
                            <span class="badge badge-success">Diterima</span>
                            @break
                        @case('ditolak')
                            <span class="badge badge-danger">Ditolak</span>
                            @break
                        @case('pending')
                            <span class="badge badge-warning">Pending</span>
                            @break
                        @default
                            <span class="badge badge-secondary">{{ $pengajuan->status }}</span>
                    @endswitch
                </p>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" onclick="$('#myModal').modal('hide')">Tutup</button>
</div>