<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DosenModel;
use App\Models\LamaranMagangModel;
use App\Models\MahasiswaModel;
use App\Models\NotifikasiModel;
use App\Models\PerusahaanModel;
use App\Models\ProdiModel;
use App\Models\LowonganMagangModel;
use App\Models\LowonganModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Exception; // Import Exception class
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PengajuanMagangMhsController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Pengajuan Magang',
            'list'  => ['Home', 'Pengajuan Magang']
        ];

        $page = (object) [
            'title' => 'Data Pengajuan Magang'
        ];

        $activeMenu = 'pengajuan_magang_mhs'; // set menu yang sedang aktif

        return view('pengajuan_magang_mhs.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list()
    {
        try {
            $lamaran = LamaranMagangModel::with('lowongan', 'dosen', 'mahasiswa')
                ->get();

            $lamaran = $lamaran->where('mhs_nim', auth()->user()->mahasiswa->mhs_nim);

            return DataTables::of($lamaran)
                ->addIndexColumn()
                ->addColumn('mhs_nim', function ($lamaran) {
                    return $lamaran->mahasiswa ? $lamaran->mahasiswa->mhs_nim : '-';
                })
                ->addColumn('dosen_nama', function ($lamaran) {
                    return $lamaran->dosen ? $lamaran->dosen->nama : '-';
                })
                ->addColumn('tanggal_lamaran', function ($lamaran) {
                    return $lamaran->tanggal_lamaran ?? '-';
                })
                ->addColumn('lowongan_nama', function ($lamaran) {
                    // Pastikan lowongan tidak null sebelum mengakses properti judul
                    return $lamaran->lowongan ? $lamaran->lowongan->judul : '-';
                })
                ->addColumn('status', function ($lamaran) {
                    return $lamaran->status ?? '-';
                })
                ->addColumn('aksi', function ($lmr) {
                    $btn  = '<div class="btn-group" role="group">';
                    $btn .= '<button onclick="modalAction(\'' . url('/pengajuan-magang-mhs/' . $lmr->lamaran_id . '/show_ajax') . '\')" class="btn btn-primary btn-sm" style="margin-right: 5px;" title="Detail Data">';
                    $btn .= '<i class="fas fa-info-circle"></i></button>';
                    // Uncomment baris di bawah jika Anda ingin mengaktifkan tombol edit dan hapus
                    // $btn .= '<button onclick="modalAction(\''.url('/pengajuan-magang-mhs/' . $lmr->lamaran_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm" style="margin-right: 5px;" title="Edit Data">';
                    // $btn .= '<i class="fas fa-edit"></i></button>';
                    // $btn .= '<button onclick="modalAction(\''.url('/pengajuan-magang-mhs/' . $lmr->lamaran_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm" title="Hapus Data">';
                    // $btn .= '<i class="fas fa-trash-alt"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('DataTables AJAX error in PengajuanMagangMhsController@list: ' . $e->getMessage());
            // Return a JSON error response that DataTables can understand
            return response()->json([
                'draw'            => 0,
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ], 500); // Mengembalikan status HTTP 500 untuk error server
        }
    }

    public function show_ajax($id)
    {
        // Get the internship application with related data using model relationships
        $pengajuan = LamaranMagangModel::with([
            'mahasiswa',
            'dosen',
            'lowongan'
        ])
            ->findOrFail($id);

        // Format the data for the view
        $data = [
            'lamaran_id' => $pengajuan->lamaran_id,
            'mhs_nim' => $pengajuan->mhs_nim,
            'mhs_nama' => $pengajuan->mahasiswa->nama ?? '-',
            'dosen_nama' => $pengajuan->dosen->nama ?? 'Belum ditentukan',
            'lowongan_nama' => $pengajuan->lowongan->judul ?? '-',
            'tanggal_lamaran' => $pengajuan->tanggal_lamaran,
            'status' => $pengajuan->status,
            'dosen_id' => $pengajuan->dosen_id
        ];

        return view('pengajuan_magang_mhs.show_ajax', ['pengajuan' => (object)$data]);
    }


    public function update_status(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'dosen_id' => 'required_if:status,diterima|nullable|exists:m_dosen,dosen_id'
        ], [
            'dosen_id.required_if' => 'Dosen pembimbing wajib dipilih jika lamaran diterima.'
        ]);

        $lamaran = LamaranMagangModel::findOrFail($id);
        $lamaran->status = $request->status;
        $lamaran->dosen_id = $request->status === 'diterima' ? $request->dosen_id : null;

        // Pastikan lowongan ada sebelum mengakses properti perusahaan_id
        $nama_perusahaan = '-';
        if ($lamaran->lowongan) {
            $perusahaan = PerusahaanModel::select('nama')->where('perusahaan_id', $lamaran->lowongan->perusahaan_id)->first();
            if ($perusahaan) {
                $nama_perusahaan = $perusahaan->nama;
            }
        }

        if ($request->status === 'diterima') {
            MahasiswaModel::where('mhs_nim', $lamaran->mhs_nim)->update(['status_magang' => "Sedang Magang"]);
            NotifikasiModel::create([
                'mhs_nim' => $lamaran->mhs_nim,
                'lamaran_id' => $lamaran->lamaran_id,
                'judul' => 'Pemberitahuan Lamaran Magang',
                'pesan' => 'Selamat, lamaran magang Anda pada ' . $nama_perusahaan . ' telah diterima.',
                'waktu_dibuat' => now()
            ]);
        } else if ($request->status === 'ditolak') {
            NotifikasiModel::create([
                'mhs_nim' => $lamaran->mhs_nim,
                'lamaran_id' => $lamaran->lamaran_id,
                'judul' => 'Pemberitahuan Lamaran Magang',
                'pesan' => 'Maaf, lamaran magang Anda pada ' . $nama_perusahaan . ' telah ditolak.',
                'waktu_dibuat' => now()
            ]);
        }
        $lamaran->save();

        return response()->json([
            'status' => true,
            'message' => 'Status lamaran berhasil diperbarui.'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'mhs_nim' => 'required|string|exists:m_mahasiswa,mhs_nim',
            'lowongan_id' => 'required|integer|exists:t_lowongan_magang,lowongan_id',
            'tanggal_lamaran' => 'required|date',
        ]);

        try {
            LamaranMagangModel::create([
                'mhs_nim' => $request->mhs_nim,
                'lowongan_id' => $request->lowongan_id,
                'tanggal_lamaran' => Carbon::parse($request->tanggal_lamaran),
                'status' => 'pending',
                'dosen_id' => null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Pengajuan magang berhasil diajukan. Menunggu persetujuan.'
            ]);
        } catch (Exception $e) {
            Log::error('Error creating new internship application: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengajukan magang: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkStatus($lowongan_id)
    {
        try {
            $mahasiswa = Auth::user()->mahasiswa;

            // Check if the user has any application with 'diterima' status
            $existingDiterima = LamaranMagangModel::where('mhs_nim', $mahasiswa->mhs_nim)
                ->where('status', 'diterima')
                ->first();

            if ($existingDiterima) {
                return response()->json([
                    'status' => 'diterima_existing',
                    'lowongan_judul' => $existingDiterima->lowongan->judul
                ]);
            }

            // Check the specific lowongan_id for 'selesai' or 'pending' status
            $existingLamaran = LamaranMagangModel::where('mhs_nim', $mahasiswa->mhs_nim)
                ->where('lowongan_id', $lowongan_id)
                ->first();

            if ($existingLamaran) {
                if ($existingLamaran->status === 'selesai') {
                    return response()->json([
                        'status' => 'selesai'
                    ]);
                } elseif ($existingLamaran->status === 'pending') {
                    return response()->json([
                        'status' => 'pending',
                        'lowongan_judul' => $existingLamaran->lowongan->judul
                    ]);
                }
            }

            return response()->json([
                'status' => 'available'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat memeriksa status'], 500);
        }
    }

    public function edit_ajax(String $lamaran_id)
    {
        $lamaran = LamaranMagangModel::with('lowongan', 'dosen', 'mahasiswa')
            ->where('lamaran_id', $lamaran_id)
            ->first();

        // Pastikan relasi mahasiswa dan lowongan ada sebelum mengakses propertinya
        if (!$lamaran || !$lamaran->mahasiswa || !$lamaran->lowongan) {
            return response()->json([
                'status' => false,
                'message' => 'Data lamaran atau relasi terkait tidak ditemukan.'
            ], 404);
        }

        $prodi = ProdiModel::find($lamaran->mahasiswa->prodi_id);
        $perusahaan = PerusahaanModel::find($lamaran->lowongan->perusahaan_id);
        $dosens = DosenModel::all();

        if (!$prodi) {
            return response()->json([
                'status' => false,
                'message' => 'Data prodi tersebut tidak ditemukan.'
            ], 404);
        }
        if (!$perusahaan) {
            return response()->json([
                'status' => false,
                'message' => 'Data perusahaan tersebut tidak ditemukan.'
            ], 404);
        }
        return view('pengajuan_magang_mhs.edit_ajax', [ // Pastikan path view sesuai
            'lamaran' => $lamaran,
            'prodi' => $prodi,
            'perusahaan' => $perusahaan,
            'dosens' => $dosens

        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak,pending,selesai',
            'dosen_id' => 'required_if:status,diterima|nullable|exists:m_dosen,dosen_id'
        ], [
            'status.in' => 'Status harus berupa diterima, ditolak, pending, atau selesai.',
            'dosen_id.required_if' => 'Dosen pembimbing wajib dipilih jika lamaran diterima.'
        ]);

        $lamaran = LamaranMagangModel::findOrFail($id);
        $lamaran->status = $request->status;
        $lamaran->dosen_id = ($request->status === 'diterima' || $request->status === 'selesai') ? $request->dosen_id : null;

        // Pastikan lowongan ada sebelum mengakses properti perusahaan_id
        $nama_perusahaan = '-';
        if ($lamaran->lowongan) {
            $perusahaan = PerusahaanModel::select('nama')->where('perusahaan_id', $lamaran->lowongan->perusahaan_id)->first();
            if ($perusahaan) {
                $nama_perusahaan = $perusahaan->nama;
            }
        }

        if ($request->status === 'diterima') {
            MahasiswaModel::where('mhs_nim', $lamaran->mhs_nim)->update(['status_magang' => "Sedang Magang"]);
            NotifikasiModel::create([
                'mhs_nim' => $lamaran->mhs_nim,
                'lamaran_id' => $lamaran->lamaran_id,
                'judul' => 'Pemberitahuan Lamaran Magang',
                'pesan' => 'Selamat, lamaran magang Anda pada ' . $nama_perusahaan . ' telah dirubah menjadi diterima.',
                'waktu_dibuat' => now()
            ]);
        } else if ($request->status === 'pending' || $request->status === 'ditolak') { // Konsolidasi kondisi ditolak di sini
            MahasiswaModel::where('mhs_nim', $lamaran->mhs_nim)->update(['status_magang' => "Belum Magang"]);
            NotifikasiModel::create([
                'mhs_nim' => $lamaran->mhs_nim,
                'lamaran_id' => $lamaran->lamaran_id,
                'judul' => 'Pemberitahuan Lamaran Magang',
                'pesan' => 'Maaf, lamaran magang Anda pada ' . $nama_perusahaan . ' telah dirubah menjadi ' . $request->status . '.', // Pesan lebih umum
                'waktu_dibuat' => now()
            ]);
        } else if ($request->status === 'selesai') {
            MahasiswaModel::where('mhs_nim', $lamaran->mhs_nim)->update(['status_magang' => "Selesai Magang"]);
            NotifikasiModel::create([
                'mhs_nim' => $lamaran->mhs_nim,
                'lamaran_id' => $lamaran->lamaran_id,
                'judul' => 'Pemberitahuan Lamaran Magang',
                'pesan' => 'Selamat, magang Anda pada ' . $nama_perusahaan . ' telah selesai.',
                'waktu_dibuat' => now()
            ]);
        }
        // Removed the redundant 'else if($request->status === 'ditolak')' block here.

        $lamaran->save();

        return response()->json([
            'status' => true,
            'message' => 'Lamaran berhasil diperbarui.'
        ]);
    }

    public function create_ajax($id)
    {
        // Ambil data mahasiswa yang login
        $mahasiswa = auth()->user()->mahasiswa;


        // Lanjutkan proses jika CV sudah ada
        $lowongan = LowonganModel::find($id);
        $dosen = DosenModel::all();

        return view('rekomendasi.create_ajax', [
            'lowongan' => $lowongan,
            'dosen' => $dosen,
            'breadcrumb' => (object)['title' => 'Lamaran Magang']
        ]);
    }
    public function confirm_ajax(String $lamaran_id)
    {
        $lamaran = LamaranMagangModel::with('lowongan', 'dosen', 'mahasiswa')
            ->where('lamaran_id', $lamaran_id)
            ->first();

        // Pastikan relasi mahasiswa dan lowongan ada sebelum mengakses propertinya
        if (!$lamaran || !$lamaran->mahasiswa || !$lamaran->lowongan) {
            return response()->json([
                'status' => false,
                'message' => 'Data lamaran atau relasi terkait tidak ditemukan.'
            ], 404);
        }

        $prodi = ProdiModel::find($lamaran->mahasiswa->prodi_id);
        $perusahaan = PerusahaanModel::find($lamaran->lowongan->perusahaan_id);
        $dosens = DosenModel::all();

        if (!$prodi) {
            return response()->json([
                'status' => false,
                'message' => 'Data prodi tersebut tidak ditemukan.'
            ], 404);
        }
        if (!$perusahaan) {
            return response()->json([
                'status' => false,
                'message' => 'Data perusahaan tersebut tidak ditemukan.'
            ], 404);
        }
        return view('pengajuan_magang_mhs.confirm_ajax', [ // Pastikan path view sesuai
            'lamaran' => $lamaran,
            'prodi' => $prodi,
            'perusahaan' => $perusahaan,
            'dosens' => $dosens

        ]);
    }

    public function delete_ajax(Request $request, $lamaran_id)
    {
        try {
            $lamaran = LamaranMagangModel::where('lamaran_id', $lamaran_id)->first();

            if (!$lamaran) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data lamaran tidak ditemukan.'
                ], 404);
            }

            // Soft delete lamaran
            $lamaran->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data lamaran berhasil dihapus.'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'status' => false,
                    'message' => 'Data gagal dihapus karena masih digunakan pada data lain.'
                ], 422);
            }
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restore(Request $request, $lamaran_id)
    {
        try {
            $lamaran = LamaranMagangModel::withTrashed()->where('lamaran_id', $lamaran_id)->first();

            if (!$lamaran) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data lamaran tidak ditemukan.'
                ], 404);
            }

            $lamaran->restore();

            return response()->json([
                'status' => true,
                'message' => 'Data lamaran berhasil dipulihkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memulihkan data: ' . $e->getMessage()
            ], 500);
        }
    }
}
