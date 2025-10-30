<?php

// app/Http/Controllers/LowonganController.php

namespace App\Http\Controllers;

use App\Models\BidangKeahlianModel;
use Illuminate\Http\Request;
use App\Models\LowonganModel;
use App\Models\PerusahaanModel;
use App\Models\PeriodeMagangModel;
use Illuminate\Support\Facades\Auth;
use App\Models\MahasiswaModel;
use App\Models\ProvinsiModel;
use App\Models\SkillModel;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Services\SmartRecommendationService;
use Illuminate\Support\Facades\Storage;    
use Illuminate\Support\Facades\Log; 

class LowonganController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Lowongan Magang',
            'list'  => ['Home', 'Lowongan']
        ];
        $page = (object)['title' => 'Manajemen Lowongan Magang'];
        $activeMenu = 'lowongan';

        return view('lowongan.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {

        // 1) Deactivate any lowongan whose deadline has passed
        LowonganModel::where('status', 'aktif')
        ->where('deadline_lowongan', '<', Carbon::today()->toDateString())
        ->update(['status' => 'nonaktif']);

        if ($request->ajax()) {
            $q = LowonganModel::with(['perusahaan', 'periode'])
                ->where('status', 'aktif')       // ⬅ hanya ambil yang aktif
                ->select(
                    'lowongan_id',
                    'judul',
                    'deskripsi',
                    'tanggal_mulai_magang',
                    'deadline_lowongan',
                    'lokasi',
                    'perusahaan_id',
                    'periode_id',
                    'sylabus_path',
                    'status',
                    'tipe_bekerja',
                    'kuota',
                    'durasi'
                );

            return DataTables::of($q)
                ->addIndexColumn()
                ->addColumn('perusahaan', fn($row) => $row->perusahaan->nama ?? '-')
                ->addColumn('periode', fn($row) => $row->periode->periode ?? '-')
                ->addColumn('lokasi', fn($row) => $row->provinsi->alt_name ?? '-')
                ->addColumn('aksi', function ($row) {
                    $url = url("/lowongan/{$row->lowongan_id}");
                    return "
                       <div class='btn-group'>
                         <button onclick=\"modalAction('" . url('/lowongan/' . $row->lowongan_id . '/show_ajax') . "')\" class='btn btn-info btn-sm'><i class='fas fa-info-circle'></i></button>
                         <button onclick=\"modalAction('" . url('/lowongan/' . $row->lowongan_id . '/edit_ajax') . "')\" class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></button>
                         <button onclick=\"modalAction('" . url('/lowongan/' . $row->lowongan_id . '/delete_ajax') . "')\" class='btn btn-danger btn-sm'><i class='fas fa-trash'></i></button>
                       </div>";
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function create_ajax()
    {
        $perusahaan = PerusahaanModel::all();
        $periode    = PeriodeMagangModel::all();
        $provinsi   = ProvinsiModel::all();
        return view('lowongan.create_ajax', compact('perusahaan', 'periode', 'provinsi'));
    }

public function store_ajax(Request $request)
{
    // 1. Validation
    $validator = Validator::make($request->all(), [
        'judul'                => 'required|string|max:255',
        'deskripsi'            => 'required|string',
        'tanggal_mulai_magang' => 'required|date',
        'deadline_lowongan'    => 'required|date|after_or_equal:tanggal_mulai_magang',
        'lokasi'               => 'required|integer|exists:m_provinsi,id',
        'perusahaan_id'        => 'required|exists:m_perusahaan_mitra,perusahaan_id',
        'periode_id'           => 'required|exists:m_periode_magang,periode_id',
        'sylabus_file'         => 'nullable|file|mimes:pdf|max:2048',
        'status'               => 'nullable|in:aktif,nonaktif',
        'tipe_bekerja'         => 'nullable|string',
        'kuota'                => 'nullable|integer|min:0',
        'durasi'               => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'   => false,
            'msgField' => $validator->errors(),
            'message'  => 'Validasi gagal'
        ]);
    }

    // 2. Gather data
    $data = $request->only([
        'judul','deskripsi','tanggal_mulai_magang','deadline_lowongan',
        'lokasi','perusahaan_id','periode_id','status',
        'tipe_bekerja','kuota','durasi',
    ]);

    // 3. Handle optional PDF upload
    if ($request->hasFile('sylabus_file') && $request->file('sylabus_file')->isValid()) {
        // Delete old if any (for update you already handle this separately)
        $data['sylabus_path'] = $request
            ->file('sylabus_file')
            ->store('sylabus', 'public');
    }

    // 4. Create record
    LowonganModel::create($data);

    // 5. Return success
    return response()->json([
        'status'  => true,
        'message' => 'Lowongan berhasil ditambahkan'
    ]);
}


    public function confirm_ajax($lowongan_id)
    {
        $lowongan = LowonganModel::find($lowongan_id);
        return view('lowongan.confirm_ajax', compact('lowongan'));
    }

    public function delete_ajax(Request $request, $lowongan_id)
    {
        $l = LowonganModel::find($lowongan_id);
        if (!$l) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        // bukan delete(), tapi update status
        $l->update(['status' => 'nonaktif']);

        return response()->json([
            'status'  => true,
            'message' => 'Lowongan berhasil dinonaktifkan'
        ]);
    }

    public function show_ajax($lowongan_id)
    {
        $lowongan = LowonganModel::with(['perusahaan', 'periode', 'provinsi'])
            ->find($lowongan_id);
        if (!$lowongan) abort(404, 'Not Found');

        // convert string ke Carbon
        $lowongan->tanggal_mulai_magang = Carbon::parse($lowongan->tanggal_mulai_magang);
        $lowongan->deadline_lowongan    = Carbon::parse($lowongan->deadline_lowongan);
        return view('lowongan.show_ajax', compact('lowongan'));
    }

    public function edit_ajax($lowongan_id)
    {
        $lowongan = LowonganModel::find($lowongan_id);
        $perusahaan = PerusahaanModel::all();
        $periode    = PeriodeMagangModel::all();
        $provinsi   = ProvinsiModel::all();
        return view('lowongan.edit_ajax', compact('lowongan', 'perusahaan', 'periode', 'provinsi'));
    }

public function update_ajax(Request $request, $lowongan_id)
{
    $lowongan = LowonganModel::find($lowongan_id);

    if (! $lowongan) {
        return response()->json([
            'status'  => false,
            'message' => 'Data tidak ditemukan'
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'judul'                => 'required|string|max:255',
        'deskripsi'            => 'required|string',
        'tanggal_mulai_magang' => 'required|date',
        'deadline_lowongan'    => 'required|date|after_or_equal:tanggal_mulai_magang',
        'lokasi'               => 'required|integer|exists:m_provinsi,id',
        'perusahaan_id'        => 'required|exists:m_perusahaan_mitra,perusahaan_id',
        'periode_id'           => 'required|exists:m_periode_magang,periode_id',
        'sylabus_file'         => 'nullable|file|mimes:pdf|max:2048',
        'status'               => 'nullable|in:aktif,nonaktif',
        'tipe_bekerja'         => 'nullable|string',
        'kuota'                => 'nullable|integer|min:0',
        'durasi'               => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'   => false,
            'msgField' => $validator->errors(),
            'message'  => 'Validasi gagal'
        ]);
    }

    // Prepare data for update
    $updateData = $request->only([
        'judul',
        'deskripsi',
        'tanggal_mulai_magang',
        'deadline_lowongan',
        'lokasi',
        'perusahaan_id',
        'periode_id',
        'status',
        'tipe_bekerja',
        'kuota',
        'durasi',
    ]);

    // If a new PDF is uploaded, delete old one and store the new file
    if ($request->hasFile('sylabus_file')) {
        if ($lowongan->sylabus_path && Storage::disk('public')->exists($lowongan->sylabus_path)) {
            Storage::disk('public')->delete($lowongan->sylabus_path);
        }
        $updateData['sylabus_path'] = $request
            ->file('sylabus_file')
            ->store('sylabus', 'public');
    }

    // Apply the update
    $lowongan->update($updateData);

    return response()->json([
        'status'  => true,
        'message' => 'Lowongan berhasil diperbarui'
    ]);
}

public function rekomendasi(Request $request, SmartRecommendationService $smart)
{
    // 1) Deactivate any lowongan whose deadline has passed
    LowonganModel::where('status', 'aktif')
        ->where('deadline_lowongan', '<', Carbon::today()->toDateString())
        ->update(['status' => 'nonaktif']);

    // ---------------------------
    // 1. Ambil data mahasiswa
    // ---------------------------
    $mhs = MahasiswaModel::where('user_id', Auth::id())
         ->with('provinsipref', 'skills')
         ->firstOrFail();

    // Pecah keyword pref & skill
    // $prefKeywords  = array_filter(array_map('trim', explode(',', $mhs->pref)));
    $prefFrases = array_filter(array_map('trim', explode(',', $mhs->pref)));
    $prefKeywords = [];
    foreach ($prefFrases as $frase) {
        // explode on whitespace—bisa multi space, tab, dll
        foreach (preg_split('/\s+/', $frase) as $kata) {
            $kata = strtolower(trim($kata));
            if ($kata !== '') {
                $prefKeywords[] = $kata;
            }
        }
    }
    $prefKeywords = array_values(array_unique($prefKeywords));
    // now from pivot instead of comma‐string:
    $skillKeywords = $mhs->skills->pluck('nama')->map(fn($s)=>trim($s))->filter()->toArray();
    $totalPref     = count($prefKeywords) ?: 1;
    $totalSkill    = count($skillKeywords) ?: 1;
    $prefFrases = array_filter(array_map('trim', explode(',', $mhs->pref)));

    // Durasi & tipe_bekerja preferensi mahasiswa
    $durasiPreferensiMhs = (float) $mhs->durasi; // misal 3 atau 6
    $tipeBekerjaMhs      = strtolower($mhs->tipe_bekerja ?? '');

    // ---------------------------
    // 2. Query lowongan aktif
    // ---------------------------
    $q = LowonganModel::with(['perusahaan', 'periode', 'provinsi'])
        ->where('status', 'aktif');

    // 3. Filter tambahan (posisi, skill, lokasi, tipe_bekerja, durasi)
    if ($request->filled('posisi')) {
        $q->where('judul', 'like', '%' . $request->posisi . '%');
    }
    // 5) Filter “Skill yang cocok” (multi‐checkbox)
    if ($request->has('skills')) {
        $selectedSkills = array_filter($request->input('skills'));
        $q->where(function($sub) use ($selectedSkills) {
            foreach ($selectedSkills as $skill) {
                $sub->orWhere('deskripsi', 'like', '%' . $skill . '%');
            }
        });
    }
    if ($request->filled('lokasi')) {                // lokasinya bisa id ATAU teks
        $lok = $request->lokasi;
        $q->whereHas('provinsi', function ($q2) use ($lok) {
            $q2->where('alt_name', 'like', "%$lok%")
                ->orWhere('id', $lok);
        });
    }
    if ($request->filled('tipe_bekerja')) {
        $q->where('tipe_bekerja', $request->tipe_bekerja);
    }
    if ($request->filled('durasi')) {
        $q->where('durasi', $request->durasi);
    }

    // 4. Eksekusi query
    $lowongan = $q->get();

    $mhs->load('provinsipref'); 

    // 5. Peta ke array untuk SMART
    if (!$lowongan->isEmpty()) {
        $raw = $lowongan->map(function ($l) use (
            $mhs,
            $prefKeywords,
            $skillKeywords,
            $totalPref,
            $totalSkill,
            $durasiPreferensiMhs,
            $tipeBekerjaMhs
        ) {
            // Hitung kemunculan kata‐kata pref di judul+deskripsi
            $judulDesc = strtolower($l->judul . ' ' . $l->deskripsi);
            $matchCount = 0;
            foreach ($prefKeywords as $kata) {
                if (strpos($judulDesc, $kata) !== false) {
                    $matchCount++;
                }
            }

            // Skor pref = jumlah kata yang match / total kata
            $prefValue = $matchCount / $totalPref;

            // SKILL
            $skillMatches = 0;
            foreach ($skillKeywords as $kw) {
                if ($kw !== '' && stripos($l->deskripsi, $kw) !== false) {
                    $skillMatches++;
                }
            }
            $skillValue = $skillMatches / $totalSkill;

            $mhsProv = $mhs->provinsipref;                   // eager-loaded automatically
            $mhsLat  = $mhsProv->latitude  ?? null;
            $mhsLon  = $mhsProv->longitude ?? null;

            $lowProv = $l->provinsi;
            $lowLat  = $lowProv->latitude  ?? null;
            $lowLon  = $lowProv->longitude ?? null;

            $lokasiValue = 0.0;
            if ($mhsProv && $lowProv) {
                $dist = ProvinsiModel::haversineKm(
                    $mhsProv->latitude, $mhsProv->longitude,
                    $lowProv->latitude, $lowProv->longitude
                );
                    $lokasiValue = ProvinsiModel::lokasiScore($dist, 1000);  // adjust radius here
                }
            
            // TIPE_BEKERJA (binary match)
            $clean = fn($s) => strtolower(str_replace([' ', '-'], '_', trim($s)));

            $mhsType  = $clean($mhs->tipe_bekerja);
            $lowType  = $clean($l->tipe_bekerja);
            $tipeBekerjaValue = ($mhsType === $lowType) ? 1.0 : 0.0;

            // DURASI (normalized difference)
            $durasiLowongan = (float) $l->durasi;
            $minDurasi       = 3.0;
            $maxDurasi       = 6.0;
            $rangeDurasi     = $maxDurasi - $minDurasi;
            $diff            = abs($durasiLowongan - $durasiPreferensiMhs);
            $durasiValue     = ($rangeDurasi > 0)
                ? 1.0 - ($diff / $rangeDurasi)
                : 1.0;
            $durasiValue = max(0.0, min(1.0, $durasiValue));

            return [
                'id'            => $l->lowongan_id,
                'pref'          => $prefValue,
                'skill'         => $skillValue,
                'lokasi'        => $lokasiValue,
                'tipe_bekerja'  => $tipeBekerjaValue,
                'durasi'        => $durasiValue,
            ];
        })->toArray();

        // 6. Hitung skor SMART
        $ranking = $smart->rank($raw);

        // 7. Gabungkan dan urutkan
        $ranked = collect($ranking)
            ->map(fn($r) =>
                $lowongan
                    ->firstWhere('lowongan_id', $r['id'])
                    ->setAttribute('smart_score', $r['score'])
            )
            ->sortByDesc('smart_score')
            ->values();
    } else {
        // kalau kosong, tetap buatlah koleksi kosong
        $ranked = collect([]);
    }

    // 8. Jika ini AJAX, render partial list saja:
    if ($request->ajax()) {
        $html = view('rekomendasi.partials.list', [
            'lowongan' => $ranked,
            'mhs'      => $mhs,
        ])->render();

        return response()->json(['html' => $html]);
    }
    $provinsi = ProvinsiModel::all(); 

    $allSkills = SkillModel::orderBy('nama')->get();

    $allBidang      = BidangKeahlianModel::orderBy('nama')->get();

    // 9. Bukan AJAX → kembalikan view penuh:
    return view('rekomendasi.index', [
        'breadcrumb' => (object)[
            'title' => 'Rekomendasi Magang',
            'list'  => ['Dashboard Mahasiswa', 'Rekomendasi Magang'],
        ],
        'page'       => (object)['title' => 'Rekomendasi Magang'],
        'activeMenu' => 'rekomendasi',
        'provinsi'  => $provinsi,
        'lowongan'   => $ranked,
        'allBidang'    => $allBidang, 
        'mhs'        => $mhs,
        'allSkills'  => $allSkills,
    ]);
}

public function show(Request $request, SmartRecommendationService $smart, $lowongan_id)
{
    // 1) Ambil data lowongan utama (detail)
    $lowongan = LowonganModel::with([
            'perusahaan', 
            'periode', 
            'lamaran', 
            'mahasiswa', 
            'provinsi'
        ])
        ->findOrFail($lowongan_id);

    // 2) Statistik ringkas
    $totalJobs      = LowonganModel::where('status', 'aktif')->count();
    $totalCompanies = LowonganModel::where('status', 'aktif')
                         ->distinct('perusahaan_id')
                         ->count('perusahaan_id');
    $totalPositions = LowonganModel::where('status', 'aktif')->sum('kuota');

    // 3) Ambil data mahasiswa dengan provinsi & skills pivot
    $mhs = MahasiswaModel::where('user_id', Auth::id())
            ->with(['provinsipref','skills'])
            ->firstOrFail();

    // 4) Siapkan query untuk sidebar (lowongan lain) dengan filter yang sama
    $q = LowonganModel::with(['perusahaan','periode','provinsi'])
            ->where('status','aktif');

    if ($request->filled('posisi')) {
        $q->where('judul','like','%'.$request->posisi.'%');
    }
    if ($request->has('skills')) {
        $selected = array_filter($request->input('skills'));
        $q->where(function($sub) use($selected){
            foreach($selected as $skill){
                $sub->orWhere('deskripsi','like','%'.$skill.'%');
            }
        });
    }
    if ($request->filled('lokasi')) {
        $lok = $request->lokasi;
        $q->whereHas('provinsi', function($q2) use($lok){
            $q2->where('alt_name','like',"%$lok%")
               ->orWhere('id',$lok);
        });
    }
    if ($request->filled('tipe_bekerja')) {
        $q->where('tipe_bekerja',$request->tipe_bekerja);
    }
    if ($request->filled('durasi')) {
        $q->where('durasi',$request->durasi);
    }

    // 5) Eksekusi dan SPK mapping
    $lowonganList = $q->get();

    // Siapkan kata‐kata pref dan skill pivot
    $prefFrases    = array_filter(array_map('trim', explode(',', $mhs->pref)));
    $prefKeywords  = [];
    foreach ($prefFrases as $frase) {
        foreach (preg_split('/\s+/', $frase) as $kata) {
            $kata = strtolower(trim($kata));
            if ($kata !== '') {
                $prefKeywords[] = $kata;
            }
        }
    }
    $prefKeywords       = array_values(array_unique($prefKeywords));
    $skillKeywords      = $mhs->skills->pluck('nama')
                              ->map(fn($s)=> trim($s))
                              ->filter()
                              ->toArray();
    $durasiPreferensiMhs = (float) $mhs->durasi;
    $tipeBekerjaMhs      = strtolower(str_replace([' ','-'], '_', trim($mhs->tipe_bekerja ?? '')));
    $totalPref           = count($prefKeywords) ?: 1;
    $totalSkill          = count($skillKeywords) ?: 1;

    if ($lowonganList->isEmpty()) {
        $lowonganListSorted = collect([]);
    } else {
        $raw = $lowonganList->map(function($l) use(
            $mhs,
            $prefKeywords,
            $skillKeywords,
            $totalPref,
            $totalSkill,
            $durasiPreferensiMhs,
            $tipeBekerjaMhs
        ) {
            // PREF
            $judulDesc  = strtolower($l->judul . ' ' . $l->deskripsi);
            $matchCount = 0;
            foreach ($prefKeywords as $kata) {
                if (strpos($judulDesc, $kata) !== false) {
                    $matchCount++;
                }
            }
            $prefValue = $matchCount / $totalPref;

            // SKILL
            $skillMatches = 0;
            foreach ($skillKeywords as $kw) {
                if ($kw !== '' && stripos($l->deskripsi, $kw) !== false) {
                    $skillMatches++;
                }
            }
            $skillValue = $skillMatches / $totalSkill;

            // LOKASI (Haversine)
            $lokasiValue = 0.0;
            if ($prov = $mhs->provinsipref && $lowProv = $l->provinsi) {
                $dist = ProvinsiModel::haversineKm(
                    $mhs->provinsipref->latitude,
                    $mhs->provinsipref->longitude,
                    $lowProv->latitude,
                    $lowProv->longitude
                );
                $lokasiValue = ProvinsiModel::lokasiScore($dist, 1000);
            }

            // TIPE_BEKERJA (binary match)
            $lowType = strtolower(str_replace([' ','-'], '_', trim($l->tipe_bekerja ?? '')));
            $tipeBekerjaValue = ($tipeBekerjaMhs === $lowType) ? 1.0 : 0.0;

            // DURASI (normalized)
            $durasiLow     = (float) $l->durasi;
            $minDurasi     = 3.0;
            $maxDurasi     = 6.0;
            $rangeDurasi   = $maxDurasi - $minDurasi;
            $diff          = abs($durasiLow - $durasiPreferensiMhs);
            $durasiValue   = $rangeDurasi > 0
                ? 1.0 - ($diff / $rangeDurasi)
                : 1.0;
            $durasiValue   = max(0.0, min(1.0, $durasiValue));

            return [
                'id'           => $l->lowongan_id,
                'pref'         => $prefValue,
                'skill'        => $skillValue,
                'lokasi'       => $lokasiValue,
                'tipe_bekerja' => $tipeBekerjaValue,
                'durasi'       => $durasiValue,
            ];
        })->toArray();

        // 6) Hitung dan urutkan SMART
        $ranking            = $smart->rank($raw);
        $lowonganListSorted = collect($ranking)
            ->map(fn($r) => $lowonganList
                ->firstWhere('lowongan_id', $r['id'])
                ->setAttribute('smart_score', $r['score'])
            )
            ->sortByDesc('smart_score')
            ->values();
    }

    // 7) AJAX detail partial?
    if ($request->query('ajax') === '1') {
        $html = view('rekomendasi.show', [
            'lowongan'       => $lowongan,
            'lowonganList'   => $lowonganListSorted,
            'totalJobs'      => $totalJobs,
            'totalCompanies' => $totalCompanies,
            'totalPositions' => $totalPositions,
            'breadcrumb'     => (object)[
                'title' => 'Detail Lowongan',
                'list'  => ['Dashboard Mahasiswa', 'Rekomendasi Magang', 'Detail']
            ],
            'page'       => (object)['title' => 'Detail Lowongan'],
            'activeMenu' => 'rekomendasi',
            'mhs'        => $mhs,
        ])->render();

        return response()->json(['html' => $html]);
    }

    // 8) Tampilkan full view
    return view('rekomendasi.show', [
        'lowongan'       => $lowongan,
        'lowonganList'   => $lowonganListSorted,
        'totalJobs'      => $totalJobs,
        'totalCompanies' => $totalCompanies,
        'totalPositions' => $totalPositions,
        'breadcrumb'     => (object)[
            'title' => 'Detail Lowongan',
            'list'  => ['Dashboard Mahasiswa', 'Rekomendasi Magang', 'Detail']
        ],
        'page'       => (object)['title' => 'Detail Lowongan'],
        'activeMenu' => 'rekomendasi',
        'mhs'        => $mhs,
    ]);
}

}
