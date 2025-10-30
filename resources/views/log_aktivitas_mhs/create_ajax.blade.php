<form action="{{ url('/log-aktivitas-mhs/ajax') }}" method="POST" id="form-tambah-aktivitas" autocomplete="off">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title">Tambah Log Aktivitas Mahasiswa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
    </div>
    <div class="modal-body">
        {{-- Hidden fields for lamaran_id and waktu --}}
        <input type="text" name="lamaran_id" id="lamaran_id" value="{{ $lamaran_id }}" hidden>
        <input type="text" name="waktu" id="waktu" hidden>

        {{-- KETERANGAN --}}
        <div class="form-group">
            <label for="keterangan" class="form-label">Keterangan</label>
            <input type="text" class="form-control" id="keterangan" name="keterangan" required>
            <div class="text-danger" id="error-keterangan"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal" aria-label="Batal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Set the current date in YYYY-MM-DD format
    const today = new Date().toISOString().split('T')[0];
    $('#waktu').val(today);

    // Validasi form

    $("#form-tambah-aktivitas").validate({
        rules: {
            keterangan: { required: true, maxlength: 255 },
        },
        submitHandler: function(form) {
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
               success: function(response) {
                    if(response.status) {
                        $('#myModal').modal('hide'); // Tutup modal

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });

                        // Reload DataTable
                        if ($.fn.DataTable.isDataTable('#log-table')) {
                            $('#log-table').DataTable().ajax.reload(null, false);
                        }
                    } else {
                        $('.text-danger').text(''); // reset error text
                        if(response.msgField){
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message || 'Mohon cek kembali inputan anda.'
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON) {
                        const response = xhr.responseJSON;
                        $('.text-danger').text('');
                        if(response.msgField){
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: response.message || 'Mohon cek kembali inputan anda.'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada server.'
                        });
                    }
                }
            });
            return false; // prevent default submit
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