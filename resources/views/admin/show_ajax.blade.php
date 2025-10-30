@empty($admin)
    <div id="modal-delete" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data admin tidak ditemukan
                </div>
                <a href="{{ url('/admin') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div class="modal-header">
        <h5 class="modal-title">Detail Admin</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <table class="table table-sm table-bordered table-striped">
            <tr>
                <th class="text-right col-4">Nama:</th>
                <td class="col-8">{{ $admin->nama }}</td>
            </tr>
            <tr>
                <th class="text-right">Email:</th>
                <td>{{ $admin->email }}</td>
            </tr>
            <tr>
                <th class="text-right">No. Telepon:</th>
                <td>{{ $admin->telp ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-right">Username:</th>
                <td>{{ $admin->user->username ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-right">Password:</th>
                <td>********</td>
            </tr>
        </table>
    </div>
    <div class="modal-footer">
        <button onclick="modalAction('{{ url('/admin/' . $admin->admin_id . '/edit_ajax') }}')" class="btn btn-success btn-sm">
            Edit
        </button>
        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" aria-label="Close">
            Close
        </button>
    </div>
@endempty
