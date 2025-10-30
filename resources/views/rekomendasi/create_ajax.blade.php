<div class="modal-header">
    <h5 class="modal-title" id="myModalLabel">status pengajuan</h5>
    <button type="button" class="close" onclick="$('#myModal').modal('hide')" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <form id="formPengajuanMagang" method="POST" action="{{ url('rekomendasi/store') }}">
        @csrf {{-- CSRF token untuk keamanan --}}

        <div class="form-group">
            <label for="mhs_nim">NIM Mahasiswa</label>
            <input type="text" class="form-control" id="mhs_nim" name="mhs_nim"
                value="{{ auth()->user()->mahasiswa->mhs_nim }}" readonly>
            <small class="text-danger" id="mhs_nim_error"></small>
        </div>

        <div class="form-group">
            <label for="lowongan_title">Lowongan Magang</label>
            <input type="text" class="form-control" id="lowongan_title" readonly>
            <input type="hidden" id="lowongan_id" name="lowongan_id">
            <small class="text-danger" id="lowongan_id_error"></small>
        </div>

        <div class="form-group">
            <label for="tanggal_lamaran">Tanggal Lamaran</label>
            <input type="datetime-local" class="form-control" id="tanggal_lamaran" name="tanggal_lamaran"
                value="{{ date('Y-m-d\TH:i') }}" required>
            <small class="text-danger" id="tanggal_lamaran_error"></small>
        </div>

        {{-- Status akan otomatis 'pending' di backend, tidak perlu input di sini --}}
        {{-- <input type="hidden" name="status" value="pending"> --}}

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Ajukan Magang</button>
        </div>
    </form>
</div>


<script>
    $(document).ready(function () {
        // Fungsi untuk mendekode HTML entities
        function decodeHtml(html) {
            var txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value;
        }

        // 1. Ambil lowongan_id dari URL
        const pathArray = window.location.pathname.split('/');
        const lowonganIdFromUrl = pathArray[pathArray.length - 1];

        // 2. Ambil data lowongan dari server
        const lowonganIdFromServer = {{ $lowongan ? $lowongan->lowongan_id : 'null' }};
        const lowonganTitleFromServer = "{{ $lowongan ? addslashes($lowongan->judul) : 'Lowongan tidak tersedia' }}";

        // 3. Validasi dan set nilai input
        if (!isNaN(lowonganIdFromUrl) && lowonganIdFromUrl > 0 && lowonganIdFromServer == lowonganIdFromUrl) {
            $('#lowongan_id').val(lowonganIdFromServer);
            $('#lowongan_title').val(decodeHtml(lowonganTitleFromServer)); // Dekode di sini
        } else {
            $('#lowongan_title').val('Lowongan tidak tersedia');
            $('#lowongan_id').val('');
        }
        // 4. Submit form dengan AJAX
        $('#formPengajuanMagang').on('submit', function (e) {
            e.preventDefault();
            $('.text-danger').text('');

            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.status) {
                        $('#myModal').modal('hide');
                        toastr.success(response.message);
                        $('#formPengajuanMagang')[0].reset();

                        if (typeof tablePengajuanMagang !== 'undefined') {
                            tablePengajuanMagang.ajax.reload();
                        } else {
                            location.reload();
                        }
                    } else {
                        // Ini akan menangani jika status=false tapi bukan error 409
                        toastr.error(response.message, '', {
                            "closeButton": true,
                            "progressBar": true,
                            "positionClass": "toast-top-right",
                            "timeOut": "5000",
                            "extendedTimeOut": "1000"
                        });
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            $('#' + key + '_error').text(value[0]);
                        });
                    } else if (xhr.status === 409) {
                        // Ini khusus untuk konflik (sudah ada lamaran)
                        toastr.error(xhr.responseJSON.message, '', {
                            "closeButton": true,
                            "progressBar": true,
                            "positionClass": "toast-top-right",
                            "timeOut": "10000", // Lebih lama untuk error penting
                            "extendedTimeOut": "2000",
                            "backgroundColor": "#ff4444", // Warna background merah
                            "textColor": "#ffffff" // Warna teks putih
                        });
                    } else {
                        toastr.error('Terjadi kesalahan server.', '', {
                            "closeButton": true,
                            "progressBar": true,
                            "positionClass": "toast-top-right",
                            "timeOut": "5000"
                        });
                    }
                }
            });
        });
    });
</script>