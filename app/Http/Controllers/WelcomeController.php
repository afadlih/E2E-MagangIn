<?php

namespace App\Http\Controllers;

use App\Models\BidangPenelitianModel;
use App\Models\DosenModel;
use App\Models\FeedbackModel;
use App\Models\ProdiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\MahasiswaModel;
use App\Models\LamaranMagangModel;
use App\Models\PerusahaanModel;
use App\Models\LowonganModel;

class WelcomeController extends Controller
{
    public function index_admin()
    {
        $breadcrumb = (object) [
            'title' => 'Dashboard Admin',
            'list'  => ['Home', 'Dashboard']
        ];

        $activeMenu = 'dashboard';

        // 1. Total Mahasiswa
        $totalMahasiswa = MahasiswaModel::count();

        // 2. Mahasiswa yang sudah magang (diterima)
        $mahasiswaMagang = LamaranMagangModel::where('status', 'diterima')
            ->distinct('mhs_nim')
            ->count('mhs_nim');
        
        // 3. Mahasiswa yang telah selesai magang
        $mahasiswaSelesaiMagang = LamaranMagangModel::where('status', 'selesai')
            ->distinct('mhs_nim')
            ->count('mhs_nim');

        // 4. Total Dosen Pembimbing
        $totalDosenPembimbing = LamaranMagangModel::whereHas('dosen')
            ->select('dosen_id')
            ->distinct()
            ->count('dosen_id');


        // 5. Rasio Dosen:Mhs (format: 1:X)
        $rasioDosenMhs = $totalDosenPembimbing > 0 ?
            round($mahasiswaMagang / $totalDosenPembimbing, 1) : 0;

        // 6. Statistik Bidang Peminatan
        $bidangPeminatan = BidangPenelitianModel::withCount(['dosen' => function ($query) {
            $query->has('mahasiswa');
        }])->orderBy('dosen_count', 'desc')
            ->limit(5)
            ->get();

        // 7. Statistik Prodi
        $statistikProdi = ProdiModel::withCount(['mahasiswa' => function ($query) {
            $query->whereHas('lamaran', function ($q) {
                $q->where('status', 'diterima');
            });
        }])->orderBy('mahasiswa_count', 'desc')
            ->get();

        // 8. Evaluasi Rekomendasi (rating feedback)
        $ratingRekomendasi = FeedbackModel::avg('rating');
        $totalFeedback = FeedbackModel::count();

        // 9. Tren Pendaftaran Bulanan
        $trenPendaftaran = LamaranMagangModel::selectRaw('
            YEAR(tanggal_lamaran) as year,
            MONTH(tanggal_lamaran) as month,
            COUNT(*) as total
        ')
            ->where('tanggal_lamaran', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Add last updated timestamp
        $lastUpdated = now()->format('Y-m-d H:i:s');

        return view('admin.welcome_admin', compact(
            'breadcrumb',
            'activeMenu',
            'totalMahasiswa',
            'mahasiswaMagang',
            'mahasiswaSelesaiMagang',
            'totalDosenPembimbing',
            'rasioDosenMhs',
            'bidangPeminatan',
            'statistikProdi',
            'ratingRekomendasi',
            'totalFeedback',
            'trenPendaftaran',
            'lastUpdated'
        ));
    }

    public function refreshDashboard()
    {
        // 1. Total Mahasiswa
        $totalMahasiswa = MahasiswaModel::count();

        // 2. Mahasiswa yang sudah magang (diterima)
        $mahasiswaMagang = LamaranMagangModel::where('status', 'diterima')
            ->distinct('mhs_nim')
            ->count('mhs_nim');
        
        // 3. Mahasiswa yang telah selesai magang
        $mahasiswaSelesaiMagang = LamaranMagangModel::where('status', 'selesai')
            ->distinct('mhs_nim')
            ->count('mhs_nim');

        // 4. Total Dosen Pembimbing
        $totalDosenPembimbing = LamaranMagangModel::whereNotNull('dosen_id')->count();


        // 5. Rasio Dosen:Mhs (format: 1:X)
        $rasioDosenMhs = $totalDosenPembimbing > 0 ?
            round($mahasiswaMagang / $totalDosenPembimbing, 1) : 0;

        // 8. Evaluasi Rekomendasi (rating feedback)
        $ratingRekomendasi = FeedbackModel::avg('rating');
        $totalFeedback = FeedbackModel::count();

        // Add last updated timestamp
        $lastUpdated = now()->format('Y-m-d H:i:s');

        return response()->json([
            'totalMahasiswa' => $totalMahasiswa,
            'mahasiswaMagang' => $mahasiswaMagang,
            'mahasiswaSelesaiMagang' => $mahasiswaSelesaiMagang,
            'totalDosenPembimbing' => $totalDosenPembimbing,
            'rasioDosenMhs' => $rasioDosenMhs,
            'ratingRekomendasi' => number_format($ratingRekomendasi, 1),
            'totalFeedback' => $totalFeedback,
            'lastUpdated' => $lastUpdated
        ]);
    }

    public function index_dosen()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list'  => ['Home', 'Welcome']
        ];

        $activeMenu = 'dashboard';
        $totalMhs = MahasiswaModel::count();
        $totalMhsDiterima = LamaranMagangModel::where('status', 'diterima')
            ->distinct('mhs_nim')
            ->count('mhs_nim');
        $totalPerusahaan = PerusahaanModel::count();
        $totalLowongan   = LowonganModel::count();

        return view(
            'welcome_dosen',
            compact(
                'breadcrumb',
                'activeMenu',
                'totalMhs',
                'totalMhsDiterima',
                'totalPerusahaan',
                'totalLowongan'
            )
        );
    }

    public function index_mahasiswa()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list'  => ['Home', 'Welcome']
        ];
        $activeMenu = 'dashboard';

        // 1. Cari data Mahasiswa yang sedang login
        $mhs = MahasiswaModel::where('user_id', Auth::id())->firstOrFail();

        // 2. Total Recommendations = semua lowongan dengan status "aktif"
        $totalRecommendations = LowonganModel::where('status', 'aktif')->count();

        // 3. Applications In Progress = lamaran milik mahasiswa yang status-nya belum selesai
        $inProgressApplications = LamaranMagangModel::where('mhs_nim', $mhs->mhs_nim)
            ->where('status', 'pending')
            ->count();

        // 4. Completed = all “diterima”
        $completedApplications = LamaranMagangModel::where('mhs_nim', $mhs->mhs_nim)
            ->where('status', 'diterima')
            ->count();

        // 4. Upcoming Deadlines = lowongan "aktif" whose deadline_lowongan dalam 7 hari ke depan
        $today      = Carbon::now()->startOfDay();
        $inSevenDays = Carbon::now()->addDays(7)->endOfDay();

        $upcomingDeadlines = LowonganModel::where('status', 'aktif')
            ->whereBetween('deadline_lowongan', [$today, $inSevenDays])
            ->orderBy('deadline_lowongan', 'asc')
            ->limit(10)
            ->get();

        $recentApplications = LamaranMagangModel::with('lowongan')
            ->where('mhs_nim', $mhs->mhs_nim)
            ->orderBy('tanggal_lamaran', 'desc')
            ->limit(5)
            ->get();

        return view('welcome_mahasiswa', [
            'breadcrumb'             => $breadcrumb,
            'activeMenu'             => $activeMenu,
            'totalRecommendations'   => $totalRecommendations,
            'inProgressApplications' => $inProgressApplications,
            'upcomingDeadlines'      => $upcomingDeadlines,
            'recentApplications'     => $recentApplications,
            'completedApplications'  => $completedApplications,
        ]);
    }
}