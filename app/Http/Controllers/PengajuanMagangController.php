<?php

namespace App\Http\Controllers;

use App\Models\DosenModel;
use App\Models\LamaranMagangModel;
use App\Models\MahasiswaModel;
use App\Models\NotifikasiModel;
use App\Models\PerusahaanModel;
use App\Models\ProdiModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PengajuanMagangController extends Controller
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

        $activeMenu = 'pengajuan_magang'; // set menu yang sedang aktif
        $prodis = ProdiModel::all(); // ambil data prodi untuk filter
        return view('pengajuan_magang.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'prodis' => $prodis
        ]);
    }

    public function list(Request $request)
{
    if ($request->ajax()) {
        $pengajuan = LamaranMagangModel::with(['mahasiswa.prodi', 'lowongan.perusahaan'])
            ->select('*')
            ->orderBy('tanggal_lamaran', 'DESC');

        // Filter berdasarkan prodi_id
        if ($request->prodi_id) {
            $pengajuan->whereHas('mahasiswa', function ($query) use ($request) {
                $query->where('prodi_id', $request->prodi_id);
            });
        }

        return DataTables::of($pengajuan)
            ->addIndexColumn()
            ->addColumn('mahasiswa_nama', function ($item) {
                return $item->mahasiswa->full_name ?? '-';
            })
            ->addColumn('mhs_nim', function ($item) {
                return $item->mahasiswa->mhs_nim ?? '-';
            })
            ->addColumn('prodi', function ($item) {
                return $item->mahasiswa->prodi->nama_prodi ?? '-';
            })
            ->addColumn('perusahaan_nama', function ($item) {
                return $item->lowongan->perusahaan->nama ?? '-';
            })
            ->addColumn('lowongan_judul', function ($item) {
                return $item->lowongan->judul ?? '-';
            })
            ->addColumn('tanggal_lamaran', function ($item) {
                return \Carbon\Carbon::parse($item->tanggal_lamaran)->format('d-m-Y');
            })
            ->addColumn('status', function ($item) {
                return $item->status;
            })
            ->addColumn('aksi', function ($item) {
                $urlShow = url('/pengajuan-magang/' . $item->lamaran_id . '/show_ajax');
                $urlEdit = url('/pengajuan-magang/' . $item->lamaran_id . '/edit_ajax');
                $urlDelete = url('/pengajuan-magang/' . $item->lamaran_id . '/delete_ajax');
                return '
                    <div class="btn-group" role="group">
                        <button onclick="modalAction(\'' . $urlShow . '\')" class="btn btn-primary btn-sm"><i class="fas fa-info-circle"></i></button>
                        <button onclick="modalAction(\'' . $urlEdit . '\')" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>
                        <button onclick="modalAction(\'' . $urlDelete . '\')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                    </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}


    public function show_ajax(String $lamaran_id)
    {
        $lamaran = LamaranMagangModel::with('lowongan', 'dosen', 'mahasiswa')
            ->where('lamaran_id', $lamaran_id)
            ->first();
        
        $prodi = ProdiModel::find($lamaran->mahasiswa->prodi_id);
        $perusahaan = PerusahaanModel::find($lamaran->lowongan->perusahaan_id);
        $dosens = DosenModel::all();
        if (!$lamaran) {
            return response()->json([
                'status' => false,
                'message' => 'Data lamaran tersebut tidak ditemukan.'
            ], 404);
        }
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
        return view('pengajuan_magang.show_ajax', [
            'lamaran' => $lamaran,
            'prodi' => $prodi,
            'perusahaan'=> $perusahaan,
            'dosens' => $dosens
            
        ]);
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
        $nama_perusahaan = PerusahaanModel::select('nama')->where('perusahaan_id', $lamaran->lowongan->perusahaan_id)->first();
        $nama_perusahaan = $nama_perusahaan->nama;
        if ($request->status === 'diterima') {
            MahasiswaModel::where('mhs_nim', $lamaran->mhs_nim)->update(['status_magang' => "Sedang Magang"]);
            NotifikasiModel::create([
                'mhs_nim' => $lamaran->mhs_nim,
                'lamaran_id' => $lamaran->lamaran_id,
                'judul' => 'Pemberitahuan Lamaran Magang',
                'pesan' => 'Selamat, lamaran magang Anda pada ' . $nama_perusahaan . ' telah diterima.',
                'waktu_dibuat' => now()
            ]);
            LamaranMagangModel::where('mhs_nim', $lamaran->mhs_nim)
                ->where('lamaran_id', '!=', $lamaran->lamaran_id)
                ->where('status', '!=', 'ditolak')
                ->update(['status' => 'ditolak',
                    'dosen_id' => null]);
        }else if($request->status === 'ditolak'){
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

    public function edit_ajax(String $lamaran_id)
    {
        $lamaran = LamaranMagangModel::with('lowongan', 'dosen', 'mahasiswa')
            ->where('lamaran_id', $lamaran_id)
            ->first();
        
        $prodi = ProdiModel::find($lamaran->mahasiswa->prodi_id);
        $perusahaan = PerusahaanModel::find($lamaran->lowongan->perusahaan_id);
        $dosens = DosenModel::all();
        if (!$lamaran) {
            return response()->json([
                'status' => false,
                'message' => 'Data lamaran tersebut tidak ditemukan.'
            ], 404);
        }
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
        return view('pengajuan_magang.edit_ajax', [
            'lamaran' => $lamaran,
            'prodi' => $prodi,
            'perusahaan'=> $perusahaan,
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
        $lamaran->dosen_id = $request->status === 'diterima' || $request->status === 'selesai' ? $request->dosen_id : null;
        $nama_perusahaan = PerusahaanModel::select('nama')->where('perusahaan_id', $lamaran->lowongan->perusahaan_id)->first();
        $nama_perusahaan = $nama_perusahaan->nama;
        if ($request->status === 'diterima') {
            MahasiswaModel::where('mhs_nim', $lamaran->mhs_nim)->update(['status_magang' => "Sedang Magang"]);
            NotifikasiModel::create([
                'mhs_nim' => $lamaran->mhs_nim,
                'lamaran_id' => $lamaran->lamaran_id,
                'judul' => 'Pemberitahuan Lamaran Magang',
                'pesan' => 'Selamat, lamaran magang Anda pada ' . $nama_perusahaan . ' telah dirubah menjadi diterima.',
                'waktu_dibuat' => now()
            ]);
            LamaranMagangModel::where('mhs_nim', $lamaran->mhs_nim)
                ->where('lamaran_id', '!=', $lamaran->lamaran_id)
                ->where('status', '!=', 'ditolak')
                ->update(['status' => 'ditolak',
                    'dosen_id' => null]);
        }else if($request->status === 'pending' || $request->status === 'ditolak'){
            MahasiswaModel::where('mhs_nim', $lamaran->mhs_nim)->update(['status_magang' => "Belum Magang"]);
            NotifikasiModel::create([
                'mhs_nim' => $lamaran->mhs_nim,
                'lamaran_id' => $lamaran->lamaran_id,
                'judul' => 'Pemberitahuan Lamaran Magang',
                'pesan' => 'Maaf, lamaran magang Anda pada ' . $nama_perusahaan . ' telah dirubah menjadi ' . $request->status . '(ditangguhkan).',
                'waktu_dibuat' => now()
            ]);
        }else if ($request->status === 'selesai') {
            MahasiswaModel::where('mhs_nim', $lamaran->mhs_nim)->update(['status_magang' => "Selesai Magang"]);
            NotifikasiModel::create([
                'mhs_nim' => $lamaran->mhs_nim,
                'lamaran_id' => $lamaran->lamaran_id,
                'judul' => 'Pemberitahuan Lamaran Magang',
                'pesan' => 'Selamat, magang Anda pada ' . $nama_perusahaan . ' telah selesai.',
                'waktu_dibuat' => now()
            ]);
            LamaranMagangModel::where('mhs_nim', $lamaran->mhs_nim)
                ->where('lamaran_id', '!=', $lamaran->lamaran_id)
                ->where('status', '!=', 'ditolak')
                ->update(['status' => 'ditolak',
                    'dosen_id' => null]);
        }else if($request->status === 'ditolak'){
            MahasiswaModel::where('mhs_nim', $lamaran->mhs_nim)->update(['status_magang' => "Belum Magang"]);
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
            'message' => 'Lamaran berhasil diperbarui.'
        ]);
    }
    public function confirm_ajax(String $lamaran_id)
    {
        $lamaran = LamaranMagangModel::with('lowongan', 'dosen', 'mahasiswa')
            ->where('lamaran_id', $lamaran_id)
            ->first();
        
        $prodi = ProdiModel::find($lamaran->mahasiswa->prodi_id);
        $perusahaan = PerusahaanModel::find($lamaran->lowongan->perusahaan_id);
        $dosens = DosenModel::all();
        if (!$lamaran) {
            return response()->json([
                'status' => false,
                'message' => 'Data lamaran tersebut tidak ditemukan.'
            ], 404);
        }
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
        return view('pengajuan_magang.confirm_ajax', [
            'lamaran' => $lamaran,
            'prodi' => $prodi,
            'perusahaan'=> $perusahaan,
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

    public function export_pdf()
    {
        $lamaran = LamaranMagangModel::with([
                'lowongan.perusahaan',
                'dosen',
                'mahasiswa.prodi'
            ])
            ->whereIn('status', ['diterima', 'selesai'])
            ->get();

        if ($lamaran->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Data lamaran tidak ditemukan.'
            ], 404);
        }

        $pdf = Pdf::loadView('pengajuan_magang.export_pdf', ['lamaran' => $lamaran]);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data Magang ' . now()->format('Y-m-d H:i:s') . '.pdf');
    }



    public function export_excel()
    {
        $lamaran = LamaranMagangModel::with(['mahasiswa.prodi', 'lowongan.perusahaan', 'dosen'])
            ->whereIn('status', ['diterima', 'selesai'])    
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4A90E2']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ];

        $columns = [
            'A' => 'No',
            'B' => 'NIM',
            'C' => 'Nama Mahasiswa',
            'D' => 'Prodi',
            'E' => 'No. Telepon',
            'F' => 'Judul Lowongan',
            'G' => 'Perusahaan',
            'H' => 'Alamat Perusahaan',
            'I' => 'Tanggal Lamaran',
            'J' => 'Status',
            'K' => 'Dosen Pembimbing',
            'L' => 'Email Dosen',
        ];

        $colIndex = 1;
        foreach ($columns as $col => $label) {
            $sheet->setCellValue($col . '1', $label);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze header row
        $sheet->freezePane('A2');

        // Isi data
        $row = 2;
        $no = 1;
        foreach ($lamaran as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->mahasiswa->mhs_nim);
            $sheet->setCellValue('C' . $row, $item->mahasiswa->full_name);
            $sheet->setCellValue('D' . $row, $item->mahasiswa->prodi->nama_prodi ?? '-');
            $sheet->setCellValue('E' . $row, $item->mahasiswa->telp);
            $sheet->setCellValue('F' . $row, $item->lowongan->judul ?? '-');
            $sheet->setCellValue('G' . $row, $item->lowongan->perusahaan->nama ?? '-');
            $sheet->setCellValue('H' . $row, $item->lowongan->perusahaan->alamat ?? '-');
            $sheet->setCellValue('I' . $row, \Carbon\Carbon::parse($item->tanggal_lamaran)->format('d-m-Y'));
            $sheet->setCellValue('J' . $row, ucfirst($item->status));
            $sheet->setCellValue('K' . $row, $item->dosen->nama ?? '-');
            $sheet->setCellValue('L' . $row, $item->dosen->email ?? '-');

            // Wrap text & border
            foreach (array_keys($columns) as $col) {
                $sheet->getStyle($col . $row)->getAlignment()->setWrapText(true);
                $sheet->getStyle($col . $row)->getBorders()->getAllBorders()->setBorderStyle('thin');
            }

            // Alternating row color
            if ($row % 2 == 0) {
                $sheet->getStyle("A$row:L$row")->getFill()->setFillType('solid')->getStartColor()->setRGB('F3F3F3');
            }

            $row++;
        }

        $sheet->setTitle('Data Pengajuan Magang');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Pengajuan_Magang_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Output to browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }


    // public function edit_ajax($mhs_nim)
    // {
    //     $mahasiswa = MahasiswaModel::with(['prodi', 'user'])->find($mhs_nim);

    //     return view('mahasiswa.edit_ajax', compact('mahasiswa'));
    // }

    // public function update_ajax(Request $request, $mhs_nim)
    // {
    //     $mahasiswa = MahasiswaModel::with('user')->find($mhs_nim);

    //     if (!$mahasiswa) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Data mahasiswa tidak ditemukan.'
    //         ]);
    //     }

    //     // Validasi input
    //     $validator = Validator::make($request->all(), [
    //         'username'  => 'required|max:20|unique:m_users,username,' . $mahasiswa->user->user_id . ',user_id',
    //         'password'  => 'nullable|min:5|max:20',
    //         'full_name' => 'required|max:100',
    //         'alamat'    => 'nullable|max:255',
    //         'telp'      => 'nullable|max:20',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validasi gagal, periksa input anda.',
    //             'msgField' => $validator->errors()
    //         ]);
    //     }

    //     try {
    //         // Update data mahasiswa
    //         $mahasiswa->full_name = $request->full_name;
    //         $mahasiswa->alamat = $request->alamat;
    //         $mahasiswa->telp = $request->telp;
    //         $mahasiswa->save();

    //         // Update user (username dan password)
    //         $user = $mahasiswa->user;
    //         $user->username = $request->username;
    //         if (!empty($request->password)) {
    //             $user->password = bcrypt($request->password);
    //         }
    //         $user->save();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Data mahasiswa berhasil diperbarui.'
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Terjadi kesalahan saat menyimpan data.',
    //             'error' => $e->getMessage()
    //         ]);
    //     }
    // }
}
