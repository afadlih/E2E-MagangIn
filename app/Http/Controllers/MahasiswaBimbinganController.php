<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LamaranMagangModel;
use Illuminate\Support\Facades\Auth;
use App\Models\ProdiModel;

class MahasiswaBimbinganController extends Controller
{
    public function index()
{
    $breadcrumb = (object) [
        'title' => 'Mahasiswa Bimbingan',
        'list'  => ['Home', 'Mahasiswa Bimbingan']
    ];

    $page = (object) [
        'title' => 'Daftar Mahasiswa yang Dibimbing'
    ];

    $activeMenu = 'mahasiswa_bimbingan';

    // Ambil mahasiswa yang dibimbing oleh dosen ini
    $dosenId = Auth::user()->dosen->dosen_id;

    $prodis = ProdiModel::all();

    $mahasiswa = LamaranMagangModel::with('mahasiswa')
        ->where('dosen_id', $dosenId)
        ->where('status', 'diterima')
        ->get()
        ->pluck('mahasiswa') // ambil relasi mahasiswa-nya saja
        ->unique('mhs_nim'); // buang duplikat berdasarkan NIM

    return view('mahasiswa-bimbingan.index', compact('breadcrumb', 'page', 'activeMenu', 'mahasiswa', 'prodis'));
}


    public function list(Request $request)
    {
    $dosenId = Auth::user()->dosen->dosen_id;

    $query = LamaranMagangModel::with('mahasiswa', 'lowongan')
        ->where('dosen_id', $dosenId)
        ->where('status', 'diterima');

    
   if ($request->has('prodi_id') && !empty($request->prodi_id)) {
    $query->whereHas('mahasiswa', function($q) use ($request) {
        $q->where('prodi_id', $request->prodi_id);
    });


}



    $data = $query->get();

    return datatables()->of($data)
        ->addIndexColumn()
        ->addColumn('full_name', fn($row) => $row->mahasiswa->full_name ?? '-')
        ->addColumn('mhs_nim', fn($row) => $row->mahasiswa->mhs_nim ?? '-')
        ->addColumn('prodi', fn($row) => $row->mahasiswa->prodi->nama_prodi ?? '-')
  // pastikan ada field prodi di relasi mahasiswa
        ->addColumn('status_bimbingan', fn($row) => ucfirst($row->status) ?? '-') // atau status bimbingan lain jika ada
        ->addColumn('aksi', function($row) {
            $url = route('mahasiswa-bimbingan.show_ajax', $row->mahasiswa->mhs_nim);
            return '<button onclick="modalAction(\'' . $url . '\')" class="btn btn-sm btn-primary">Detail</button>';
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }

    public function show_ajax($nim)
{
    $pengajuan = LamaranMagangModel::with(['mahasiswa', 'dosen', 'lowongan'])
        ->whereHas('mahasiswa', function ($q) use ($nim) {
            $q->where('mhs_nim', $nim);
        })
        ->where('status', 'diterima')
        ->first();

    if (!$pengajuan) {
        return response('<div class="p-3">Data tidak ditemukan atau belum diterima admin.</div>');
    }

    return view('mahasiswa-bimbingan.show_ajax', compact('pengajuan'));
}

}
