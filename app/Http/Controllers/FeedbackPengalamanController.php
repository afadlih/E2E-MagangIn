<?php

namespace App\Http\Controllers;

use App\Models\FeedbackModel;
use App\Models\LamaranMagangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class FeedbackPengalamanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Feedback Pengalaman Magang Mahasiswa',
            'list'  => ['Home', 'Log Aktivitas']
        ];

        $page = (object) [
            'title' => 'Feedback Pengalaman Magang Mahasiswa'
        ];

        $activeMenu = 'feedback_pengalaman';
        $mhs_nim = auth()->user()->mahasiswa->mhs_nim;
        // Ambil satu lamaran_id milik mahasiswa
        $lamaranSelesai = LamaranMagangModel::with(['mahasiswa', 'lowongan.perusahaan'])
            ->where('status', 'selesai')
            ->where('mhs_nim', $mhs_nim)
            ->get();

        return view('feedback_magang.index', compact('lamaranSelesai', 'breadcrumb', 'page', 'activeMenu'));
    }

    

    public function create($lamaranId)
    {
        return view('feedback_magang.create', compact('lamaranId'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'lamaran_id' => 'required|exists:t_lamaran_magang,lamaran_id',
            'keterangan' => 'required|string|max:500',
            'rating' => 'required|integer|min:1|max:5', // rating maksimal 10
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'msgField' => $validator->errors()
            ]);
        }

        $lamaran = LamaranMagangModel::find($request->lamaran_id);

        try {
            // Simpan atau update feedback
            FeedbackModel::updateOrCreate(
                [
                    'mhs_nim' => $lamaran->mhs_nim,
                    'lowongan_id' => $lamaran->lowongan_id,
                    'target_type' => 'lowongan'
                ],
                [
                    'lamaran_id' => $lamaran->lamaran_id,
                    'rating' => $request->rating,
                    'komentar' => $request->keterangan,
                    'created_at' => now()
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Feedback berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    public function partialView()
    {
        $mhs_nim = auth()->user()->mahasiswa->mhs_nim;
        $lamaranSelesai = LamaranMagangModel::with(['mahasiswa', 'lowongan.perusahaan'])
                ->where('status', 'selesai')
                ->where('mhs_nim', $mhs_nim)
                ->get();

        return view('feedback_magang.lamaran_selesai', compact('lamaranSelesai'));
    }


}
