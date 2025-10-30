@forelse($lamaranSelesai as $lamaran)
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-0 card-hover">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">{{ $lamaran->lowongan->perusahaan->nama ?? '-' }}</h6>
                    <span class="badge bg-success text-white">{{ $lamaran->status }}</span>
                </div>
                <h5 class="card-title text-primary mb-3">{{ $lamaran->lowongan->judul }}</h5>

                <div class="info-section">
                    <p class="text-secondary mb-2"><i class="fas fa-user me-2 text-primary"></i> <strong>Mahasiswa:</strong> {{ $lamaran->mahasiswa->full_name ?? '-' }}</p>
                    <p class="text-secondary mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i> <strong>Lokasi:</strong> {{ $lamaran->lowongan->lokasi ?? '-' }}</p>
                    <p class="text-secondary mb-2"><i class="fas fa-calendar-alt me-2 text-primary"></i> <strong>Mulai Magang:</strong> {{ \Carbon\Carbon::parse($lamaran->lowongan->tanggal_mulai_magang)->format('d M Y') }}</p>
                    <p class="text-secondary mb-2"><i class="fas fa-hourglass-end me-2 text-primary"></i> <strong>Deadline:</strong> {{ \Carbon\Carbon::parse($lamaran->lowongan->deadline_lowongan)->format('d M Y') }}</p>
                    <p class="text-secondary mb-2"><i class="fas fa-users me-2 text-primary"></i> <strong>Kuota:</strong> {{ $lamaran->lowongan->kuota ?? '-' }}</p>
                </div>

                <div class="mt-3 p-3 bg-light border rounded">
                    @if($lamaran->feedback)
                        <h6 class="mb-2 text-success"><i class="fas fa-comment-dots me-2"></i>Feedback Anda</h6>
                        <p class="mb-1"><strong>Rating:</strong> {{ $lamaran->feedback->rating }}/5</p>
                        <p class="mb-1"><strong>Komentar:</strong> {{ $lamaran->feedback->komentar }}</p>
                        <p class="mb-0"><strong>Dibuat pada:</strong> {{ \Carbon\Carbon::parse($lamaran->feedback->created_at)->format('d M Y, H:i') }}</p>
                    @else
                        <p class="mb-0 text-muted"><i class="fas fa-info-circle me-2"></i>Belum ada feedback untuk lamaran ini.</p>
                    @endif
                </div>

                <button class="btn btn-primary w-100 mt-3 btn-feedback" 
                    onclick="modalAction('{{ route('feedback.create', $lamaran->lamaran_id) }}')"
                    >
                    <i class="fas fa-star me-2"></i> Beri Feedback
                </button>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 text-center py-5">
        <div class="d-flex flex-column align-items-center empty-state">
            <i class="fas fa-folder-open fa-4x text-primary mb-4"></i>
            <h5 class="text-primary mb-3">Tidak Ada Lamaran yang Selesai</h5>
            <p class="text-secondary lead mb-0">
                Saat ini belum ada lamaran dengan status selesai. Silakan cek kembali nanti atau hubungi administrator jika ada pertanyaan.
            </p>
        </div>
    </div>
@endforelse
