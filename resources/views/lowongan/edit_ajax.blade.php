@empty($lowongan)
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Kesalahan</h5></div>
      <div class="modal-body">
        <div class="alert alert-danger">Data tidak ditemukan.</div>
      </div>
    </div>
  </div>
@else
<form action="{{ url('/lowongan/'.$lowongan->lowongan_id.'/update_ajax') }}" method="POST" id="form-edit-lowongan" autocomplete="off" ectype="multipart/form-data">
  @csrf
  @method('PUT')
  <div class="modal-header">
    <h5 class="modal-title">Edit Lowongan Magang</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"></button>
  </div>
  <div class="modal-body">
    {{-- Judul --}}
    <div class="form-group">
      <label for="judul">Judul</label>
      <input type="text" class="form-control" id="judul" name="judul" value="{{ $lowongan->judul }}" required>
      <div class="text-danger" id="error-judul"></div>
    </div>

    {{-- Deskripsi --}}
    <div class="form-group">
      <label for="deskripsi">Deskripsi</label>
      <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required>{{ $lowongan->deskripsi }}</textarea>
      <div class="text-danger" id="error-deskripsi"></div>
    </div>

    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="tanggal_mulai_magang">Tanggal Mulai</label>
        <input type="date" class="form-control" id="tanggal_mulai_magang" name="tanggal_mulai_magang"
               value="{{ $lowongan->tanggal_mulai_magang->format('Y-m-d') }}" required>
        <div class="text-danger" id="error-tanggal_mulai_magang"></div>
      </div>
      <div class="form-group col-md-6">
        <label for="deadline_lowongan">Deadline</label>
        <input type="date" class="form-control" id="deadline_lowongan" name="deadline_lowongan"
               value="{{ $lowongan->deadline_lowongan->format('Y-m-d') }}" required>
        <div class="text-danger" id="error-deadline_lowongan"></div>
      </div>
    </div>

    {{-- Lokasi --}}
  <div class="form-group">
    <label for="lokasi">Lokasi (Provinsi)</label>
    <select class="form-control" id="lokasi" name="lokasi" required>
      <option value="">-- Pilih Provinsi --</option>
      @foreach($provinsi as $prov)
        <option value="{{ $prov->id }}"
          {{ $lowongan->lokasi == $prov->id ? 'selected' : '' }}>
          {{ $prov->alt_name }}
        </option>
      @endforeach
    </select>
    <div class="text-danger" id="error-lokasi"></div>
  </div>

    <div class="form-row">
      {{-- Perusahaan --}}
      <div class="form-group col-md-6">
        <label for="perusahaan_id">Perusahaan</label>
        <select class="form-control" id="perusahaan_id" name="perusahaan_id" required>
          <option value="">-- Pilih Perusahaan --</option>
          @foreach($perusahaan as $p)
            <option value="{{ $p->perusahaan_id }}" {{ $lowongan->perusahaan_id==$p->perusahaan_id?'selected':'' }}>
              {{ $p->nama }}
            </option>
          @endforeach
        </select>
        <div class="text-danger" id="error-perusahaan_id"></div>
      </div>
      {{-- Periode --}}
      <div class="form-group col-md-6">
        <label for="periode_id">Periode</label>
        <select class="form-control" id="periode_id" name="periode_id" required>
          <option value="">-- Pilih Periode --</option>
          @foreach($periode as $per)
            <option value="{{ $per->periode_id }}" {{ $lowongan->periode_id==$per->periode_id?'selected':'' }}>
              {{ $per->periode }}
            </option>
          @endforeach
        </select>
        <div class="text-danger" id="error-periode_id"></div>
      </div>
    </div>

    {{-- Sylabus Path --}}
  <div class="form-group">
    <label for="sylabus_file">Sylabus (PDF max 2 MB)</label>
    @if(!empty($lowongan->sylabus_path))
      <p>Current: 
        <a href="{{ asset('storage/'.$lowongan->sylabus_path) }}" target="_blank">
          {{ basename($lowongan->sylabus_path) }}
        </a>
      </p>
    @endif
    <input
      type="file"
      class="form-control"
      id="sylabus_file"
      name="sylabus_file"
      accept="application/pdf"
    >
    <span class="text-danger" id="error-sylabus_file"></span>
  </div>

    {{-- Status --}}
    <div class="form-group">
      <label for="status">Status</label>
      <select class="form-control" id="status" name="status" required>
        <option value="aktif" {{ $lowongan->status=='aktif'?'selected':'' }}>Aktif</option>
        <option value="nonaktif" {{ $lowongan->status=='nonaktif'?'selected':'' }}>Nonaktif</option>
      </select>
      <div class="text-danger" id="error-status"></div>
    </div>

    <div class="form-row">

      {{-- Kuota --}}
      <div class="form-group col-md-4">
        <label for="kuota">Kuota</label>
        <input type="number" class="form-control" id="kuota" name="kuota"
               value="{{ $lowongan->kuota }}" min="0">
        <div class="text-danger" id="error-kuota"></div>
      </div>
      {{-- Durasi --}}
      <div class="form-group col-md-4">
        <label for="durasi">Durasi</label>
        <input type="text" class="form-control" id="durasi" name="durasi" value="{{ $lowongan->durasi }}">
        <div class="text-danger" id="error-durasi"></div>
      </div>
    </div>

    {{-- Tipe Bekerja --}}
    <div class="form-group">
      <label for="tipe_bekerja">Tipe Bekerja</label>
      <input type="text" class="form-control" id="tipe_bekerja" name="tipe_bekerja" value="{{ $lowongan->tipe_bekerja }}">
      <div class="text-danger" id="error-tipe_bekerja"></div>
    </div>
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
    <button type="submit" class="btn btn-primary">Update</button>
  </div>
</form>

<script>
$(document).ready(function() {
  $("#form-edit-lowongan").validate({
    rules: {
      judul: { required: true, maxlength: 255 },
      deskripsi: { required: true },
      tanggal_mulai_magang: { required: true, date: true },
      deadline_lowongan: { required: true, date: true },
      lokasi: { required: true },
      perusahaan_id: { required: true },
      periode_id: { required: true },
      sylabus_path: { url: true },
      status: { required: true },
      kuota: { number: true, min: 0 },
      durasi: { required: true },
      tipe_bekerja: { required: true }
    },
    errorElement: 'span',
    errorPlacement: function(error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group, .form-row').append(error);
    },
    highlight: function(element) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function(element) {
      $(element).removeClass('is-invalid');
    },
submitHandler: function(form) {
  let fd = new FormData(form);

  $.ajax({
    url: form.action,
    type: form.method,
    data: fd,
    contentType: false,    // tell jQuery not to set Content-Type
    processData: false,    // tell jQuery not to serialize
    success(response) {
      if (response.status) {
        $('#myModal').modal('hide');
        Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message });
        $('#lowongan-table').DataTable().ajax.reload(null, false);
      } else {
        $.each(response.msgField, (field, msgs) => {
          $('#error-' + field).text(msgs[0]);
        });
        Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
      }
    },
    error() {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Kesalahan server.' });
    }
  });

  return false;
}
  });
});
</script>
@endempty
