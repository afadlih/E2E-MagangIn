<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DosenModel;
use App\Models\BidangPenelitianModel;
use App\Models\UserModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Storage;


class DosenController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Dosen',
            'list'  => ['Home', 'Mahasiswa']
        ];

        $page = (object) [
            'title' => 'Data Mahasiswa'
        ];

        $activeMenu = 'mahasiswa'; // set menu yang sedang aktif

        return view('dosen.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
{
    if ($request->ajax()) {
        $query = DosenModel::select('dosen_id', 'nama', 'email', 'telp');

        if ($request->filled('bidang')) {
            $query->where('id_minat', $request->bidang);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($dsn) {
                $btn  = '<div class="btn-group" role="group">';
                $btn .= '<button onclick="modalAction(\''.url('/dosen/' . $dsn->dosen_id . '/show_ajax').'\')" class="btn btn-primary btn-sm" title="Detail Data"><i class="fas fa-info-circle"></i></button>';
                $btn .= '<button onclick="modalAction(\''.url('/dosen/' . $dsn->dosen_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm" title="Edit Data"><i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="modalAction(\''.url('/dosen/' . $dsn->dosen_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm" title="Hapus Data"><i class="fas fa-trash-alt"></i></button>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}


    public function create_ajax()
    {
        $bidang_penelitian = BidangPenelitianModel::all();  // ambil semua bidang penelitian

        return view('dosen.create_ajax', compact('bidang_penelitian'));
    }

    public function store_ajax(Request $request)
    {
        // Lakukan semua validasi terlebih dahulu
        $validated = $request->validate([
            'username' => 'required|unique:m_users,username',
            'password' => 'required',
            'nama' => 'required',
            'email' => 'required|email|unique:m_dosen,email',
            'telp' => 'nullable',
            'id_minat' => 'required|exists:d_bidang_penelitian,id_minat',
        ]);

        // Setelah validasi sukses, simpan ke m_users
        $user = UserModel::create([
            'username' => $validated['username'],
            'password' => bcrypt($validated['password']),
            'level_id' => 2,
        ]);

        // Simpan ke m_dosen
        DosenModel::create([
            'user_id' => $user->user_id,
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'telp' => $validated['telp'] ?? null,
            'id_minat' => $validated['id_minat'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data dosen berhasil disimpan'
        ]);
    }



    public function confirm_ajax($dosen_id)
    {
        $dosen = DosenModel::where('dosen_id', $dosen_id)->first();

        return view('dosen.confirm_ajax', compact('dosen'));
    }

    public function delete_ajax(Request $request, $dosen_id)
    {
        $dosen = DosenModel::where('dosen_id', $dosen_id)->first();

        if (!$dosen) {
            return response()->json([
                'status' => false,
                'message' => 'Data dosen tidak ditemukan.'
            ], 404);
        }

        // Simpan ID user dulu sebelum hapus dosen
        $userId = $dosen->user_id;

        // Hapus data dosen
        $dosen->delete();

        // Hapus user yang terkait
        UserModel::where('user_id', $userId)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data dosen berhasil dihapus.'
        ]);
    }

    public function show_ajax($dosen_id)
    {
        // Ambil data dosen beserta relasi user dan bidang minat
        $dosen = DosenModel::with(['user', 'bidangPenelitian'])->where('dosen_id', $dosen_id)->first();

        // Jika data tidak ditemukan
        if (!$dosen) {
            return response()->json([
                'status' => false,
                'message' => 'Data dosen dengan ID ' . $dosen_id . ' tidak ditemukan.'
            ], 404);
        }

        // Tampilkan view show_ajax untuk dosen
        return view('dosen.show_ajax', [
            'dosen' => $dosen
        ]);
    }


    public function edit_ajax($dosen_id)
    {
        $dosen = DosenModel::with('user', 'bidangPenelitian')->find($dosen_id);
        $bidang_penelitian = BidangPenelitianModel::all();

        return view('dosen.edit_ajax', compact('dosen', 'bidang_penelitian'));
    }

    public function update_ajax(Request $request, $dosen_id)
    {
        $dosen = DosenModel::with('user')->find($dosen_id);

        if (!$dosen) {
            return response()->json([
                'status' => false,
                'message' => 'Data dosen tidak ditemukan.'
            ]);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'username'  => 'required|max:20|unique:m_users,username,' . $dosen->user->user_id . ',user_id',
            'password'  => 'nullable|min:5|max:20',
            'nama'      => 'required|max:100',
            'email'     => 'required|email|max:100',
            'telp'      => 'nullable|max:20',
            'id_minat'  => 'nullable|exists:d_bidang_penelitian,id_minat', // validasi bidang penelitian
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal, periksa input anda.',
                'msgField' => $validator->errors()
            ]);
        }

        try {
            // Update data dosen
            $dosen->nama = $request->nama;
            $dosen->email = $request->email;
            $dosen->telp = $request->telp;
            $dosen->id_minat = $request->id_minat; // update bidang penelitian
            $dosen->save();

            // Update user
            $user = $dosen->user;
            $user->username = $request->username;
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Data dosen berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage()
            ]);
        }
    }


    public function export_pdf()
    {
        $dosen = DB::table('m_dosen')
            ->join('m_users', 'm_dosen.user_id', '=', 'm_users.user_id')
            ->join('r_auth_level', 'm_users.level_id', '=', 'r_auth_level.level_id')
            ->leftJoin('d_bidang_penelitian', 'm_dosen.id_minat', '=', 'd_bidang_penelitian.id_minat') // tambahkan join ini
            ->select(
                'm_users.username', 
                'm_dosen.nama', 
                'm_dosen.email',      
                'm_dosen.telp',   
                'r_auth_level.level_name',
                'd_bidang_penelitian.bidang as bidang_penelitian' // sesuaikan nama kolom
            )
            ->orderBy('m_dosen.dosen_id', 'asc')
            ->get();

        $pdf = Pdf::loadView('dosen.export_pdf', ['dosen' => $dosen]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data Dosen ' . date('Y-m-d H:i:s') . '.pdf');
    }


    public function export_excel()
    {
        $dosen = DosenModel::select('nama', 'user_id', 'email', 'telp', 'id_minat')
            ->with([
                'user' => function ($query) {
                    $query->select('user_id', 'username', 'level_id');
                },
                'user.level',
                'bidangPenelitian:id_minat,bidang' // sesuaikan jika nama kolom berbeda
            ])
            ->orderBy('dosen_id')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Email');
        $sheet->setCellValue('E1', 'No. Telepon');
        $sheet->setCellValue('F1', 'Level');
        $sheet->setCellValue('G1', 'Bidang Penelitian');

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;

        foreach ($dosen as $data) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $data->user->username ?? '-');
            $sheet->setCellValue('C' . $baris, $data->nama);
            $sheet->setCellValue('D' . $baris, $data->email ?? '-');
            $sheet->setCellValue('E' . $baris, $data->telp ?? '-');
            $sheet->setCellValue('F' . $baris, $data->user->level->level_name ?? '-');
            $sheet->setCellValue('G' . $baris, $data->bidangPenelitian->bidang ?? '-'); // sesuaikan nama kolom jika perlu
            $no++;
            $baris++;
        }

        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Dosen');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Dosen_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function import()
    {
        return view('dosen.import'); // Pastikan view-nya sesuai
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_dosen' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            try {
                $file = $request->file('file_dosen');
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, false, true, true);

                $insertedCount = 0;
                $existingUsernames = UserModel::pluck('username')->toArray();
                $existingEmails = DosenModel::pluck('email')->toArray();

                if (count($data) > 1) {
                    foreach ($data as $index => $row) {
                        if ($index <= 1) continue; // Skip header

                        $username  = trim($row['A']);
                        $password  = trim($row['B']);
                        $nama      = trim($row['C']);
                        $email     = trim($row['D']);
                        $telp      = trim($row['E'] ?? '');
                        $id_minat  = intval($row['F'] ?? 0); // Ambil dari kolom F

                        if (!$username || !$password || !$nama || !$email || !$id_minat) continue;
                        if (in_array($username, $existingUsernames) || in_array($email, $existingEmails)) continue;

                        $user = UserModel::create([
                            'username' => $username,
                            'password' => bcrypt($password),
                            'level_id' => 2,
                            'created_at' => now()
                        ]);

                        DosenModel::create([
                            'user_id' => $user->user_id,
                            'nama' => $nama,
                            'email' => $email,
                            'telp' => $telp ?: null,
                            'id_minat' => $id_minat,
                            'created_at' => now()
                        ]);

                        $insertedCount++;
                    }

                    return response()->json([
                        'status' => true,
                        'message' => "$insertedCount dosen berhasil diimport"
                    ]);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data dosen yang diimport'
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage()
                ]);
            }
        }

        return redirect('/');
    }



    public function show_dosen($dosen_id)
    {
        // Ambil data dosen beserta relasi user dan bidangPenelitian-nya
        $dosen = DosenModel::with(['user', 'bidangPenelitian'])->where('dosen_id', $dosen_id)->first();

        // Jika data tidak ditemukan
        if (!$dosen) {
            return response()->json([
                'status' => false,
                'message' => 'Data dosen dengan ID ' . $dosen_id . ' tidak ditemukan.'
            ], 404);
        }

        // Tampilkan view show_ajax untuk dosen
        return view('dosen.show_dosen', [
            'dosen' => $dosen
        ]);
    }


    public function edit_dosen($dosen_id)
    {
        $dosen = DosenModel::with('user')->find($dosen_id);
        $bidang_penelitian = BidangPenelitianModel::all();

        return view('dosen.edit_dosen', compact('dosen', 'bidang_penelitian'));
    }

    public function update_dosen(Request $request, $dosen_id)
{
    $dosen = DosenModel::with('user')->find($dosen_id);

    if (!$dosen) {
        return response()->json([
            'status' => false,
            'message' => 'Data dosen tidak ditemukan.'
        ]);
    }

    $validator = Validator::make($request->all(), [
        'username'  => 'required|max:20|unique:m_users,username,' . $dosen->user->user_id . ',user_id',
        'password'  => 'nullable|min:5|max:20',
        'nama'      => 'required|max:100',
        'email'     => 'required|email|max:100',
        'telp'      => 'nullable|max:20',
        'id_minat'  => 'nullable|exists:d_bidang_penelitian,id_minat',
        'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi file
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validasi gagal, periksa input anda.',
            'msgField' => $validator->errors()
        ]);
    }

    try {
        // Simpan data dosen
        $dosen->nama = $request->nama;
        $dosen->email = $request->email;
        $dosen->telp = $request->telp;
        $dosen->id_minat = $request->id_minat;

        // Upload file jika ada
        if ($request->hasFile('profile_picture')) {
            // Hapus file lama jika ada
            if ($dosen->profile_picture && Storage::exists($dosen->profile_picture)) {
                Storage::delete($dosen->profile_picture);
            }

            // Simpan file baru
            $path = $request->file('profile_picture')->store('profile_dosen', 'public');
            $dosen->profile_picture = $path;

        }

        $dosen->save();

        // Update user
        $user = $dosen->user;
        $user->username = $request->username;
        if (!empty($request->password)) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Data dosen berhasil diperbarui.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Terjadi kesalahan saat menyimpan data.',
            'error' => $e->getMessage()
        ]);
    }
}

    public function hapus_foto_profile($dosen_id)
    {
        $dosen = DosenModel::find($dosen_id);

        if (!$dosen) {
            return response()->json([
                'status' => false,
                'message' => 'Data dosen tidak ditemukan.'
            ]);
        } 

        try {
            // Cek apakah dosen memiliki foto profil
            if ($dosen->profile_picture && Storage::disk('public')->exists($dosen->profile_picture)) {
                // Hapus file dari storage
                Storage::disk('public')->delete($dosen->profile_picture);
            }

            // Kosongkan kolom profile_picture di database
            $dosen->profile_picture = null;
            $dosen->save();

            return response()->json([
                'status' => true,
                'message' => 'Foto profil dosen berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus foto profil.',
                'error' => $e->getMessage()
            ]);
        }
    }


}