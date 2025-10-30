<div class="modal-header">
    <h5 class="modal-title">Detail Mahasiswa Bimbingan</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#myModal').modal('hide')">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label><strong>NIM Mahasiswa:</strong></label>
                <p>{{ $pengajuan->mahasiswa->mhs_nim ?? '-' }}</p>
            </div>

            <div class="form-group">
                <label><strong>Nama Mahasiswa:</strong></label>
                <p>{{ $pengajuan->mahasiswa->full_name ?? '-' }}</p>
            </div>

            <div class="form-group">
                <label><strong>Program Studi:</strong></label>
                <p>{{ $pengajuan->mahasiswa->prodi->nama_prodi ?? '-' }}</p>
            </div>

        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label><strong>Judul Lowongan:</strong></label>
                <p>{{ $pengajuan->lowongan->judul ?? '-' }}</p>
            </div>

            <div class="form-group">
                <label><strong>Tanggal Disetujui:</strong></label>
                <p>{{ \Carbon\Carbon::parse($pengajuan->tanggal_lamaran)->translatedFormat('d F Y H:i') }}</p>
            </div>

            <div class="form-group">
                <label><strong>Status:</strong></label>
                <p><span class="badge badge-success">Diterima</span></p>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" onclick="$('#myModal').modal('hide')">Tutup</button>
</div>
