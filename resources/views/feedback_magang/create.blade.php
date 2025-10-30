<form action="{{ route('feedback.store') }}" method="POST" id="form-feedback" autocomplete="off">
    @csrf
    <input type="hidden" name="lamaran_id" value="{{ $lamaranId }}">

    <div class="modal-header">
        <h5 class="modal-title">Beri Feedback</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        <div class="form-group">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea name="keterangan" id="keterangan" class="form-control" required></textarea>
            <div class="text-danger" id="error-keterangan"></div>
        </div>

        <div class="form-group">
            <label for="rating" class="form-label d-block">Rating</label>
            <div id="rating-stars" class="mb-2">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="fa fa-star star-rating" data-value="{{ $i }}" aria-label="Rate {{ $i }} stars"></i>
                @endfor
            </div>
            <input type="hidden" name="rating" id="rating" required>
            <div class="text-danger" id="error-rating"></div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Kirim Feedback</button>
    </div>
</form>

<style>
    .star-rating {
        font-size: 1.5rem;
        color: #ccc;
        cursor: pointer;
        transition: color 0.2s;
        margin-right: 5px;
    }
    .star-rating.selected,
    .star-rating.hovered {
        color: #ffd700; /* Gold color */
    }
    .star-rating:focus {
        outline: 2px solid #0d6efd;
        outline-offset: 2px;
    }
</style>

<script>
$(document).ready(function () {
    // Star rating interaction
    let lastSelectedValue = 0;

    $(".star-rating").on("mouseenter", function () {
        const val = $(this).data("value");
        $(".star-rating").each(function () {
            $(this).toggleClass("hovered", $(this).data("value") <= val);
        });
    }).on("mouseleave", function () {
        $(".star-rating").removeClass("hovered");
        if (lastSelectedValue > 0) {
            $(".star-rating").each(function () {
                $(this).toggleClass("selected", $(this).data("value") <= lastSelectedValue);
            });
        }
    });

    $(".star-rating").on("click", function () {
        const val = $(this).data("value");
        $("#rating").val(val);
        lastSelectedValue = val;
        $(".star-rating").removeClass("selected");
        $(".star-rating").each(function () {
            if ($(this).data("value") <= val) {
                $(this).addClass("selected");
            }
        });
    });

    // Keyboard accessibility
    $(".star-rating").on("keydown", function (e) {
        if (e.key === "Enter" || e.key === " ") {
            $(this).trigger("click");
        }
    });

    // Form validation and AJAX submit
    $("#form-feedback").validate({
        rules: {
            keterangan: { required: true, maxlength: 500 },
            rating: { required: true },
        },
        submitHandler: function (form) {
            if ($("#rating").val() === "") {
                $("#error-rating").text("Rating harus dipilih.");
                return false;
            }
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
                success: function (response) {
                    if (response.status) {
                        $('#myModal').modal('hide'); // Update this ID to match your modal
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        $("#lamaran-selesai-container").load("{{ route('lamaran.selesai.partial') }}");
                    } else {
                        $('.text-danger').text('');
                        if (response.msgField) {
                            $.each(response.msgField, function (field, msg) {
                                $('#error-' + field).text(msg[0]);
                            });
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Mohon periksa kembali inputan Anda.'
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan pada server.'
                    });
                }
            });
            return false;
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        }
    });
});
</script>