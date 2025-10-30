@empty($dosen)
    <div id="modal-detail" class="modal-dialog modal-lg" role="document">
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
                    Data dosen tidak ditemukan
                </div>
                <a href="{{ url('/dosen') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div class="modal-header" style="background-color: #1a2e4f; color: white;">
        <h5 class="modal-title">Detail Dosen</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-profile text-center">
        {{-- Gambar Profil --}}
        @if ($dosen->profile_picture)
            <img src="{{ asset('storage/' . $dosen->profile_picture) }}" alt="Foto Profil"  class="img-thumbnail rounded-circle" style="max-width: 150px;">
        @else
            <img src="{{ asset('img/user.png') }}" alt="Foto Default"  class="img-thumbnail rounded-circle" style="max-width: 150px;">
        @endif
    </div>
    
    <div class="modal-body">

        {{-- Tabel Data Dosen --}}
        <table class="table table-sm table-bordered table-striped text-left">
            <tr>
                <th class="text-right col-4">Nama:</th>
                <td class="col-8">{{ $dosen->nama }}</td>
            </tr>
            <tr>
                <th class="text-right">Email:</th>
                <td>{{ $dosen->email }}</td>
            </tr>
            <tr>
                <th class="text-right">No. Telepon:</th>
                <td>{{ $dosen->telp ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-right">Username:</th>
                <td>{{ $dosen->user->username ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-right">Bidang Penelitian:</th>
                <td>{{ $dosen->bidangPenelitian->bidang ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-right">Password:</th>
                <td>********</td>
            </tr>
        </table>
    </div>
    <div class="modal-footer">
        <button onclick="modalAction('{{ url('/dosen/' . $dosen->dosen_id . '/edit_dosen') }}')" class="btn btn-success btn-sm">
            Edit
        </button>
        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" aria-label="Close">
            Close
        </button>
    </div>
@endempty
