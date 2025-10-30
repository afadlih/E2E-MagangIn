<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminModel;
use App\Models\UserModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Storage;


class AdminController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Admin',
            'list'  => ['Home', 'Admin']
        ];

        $page = (object) [
            'title' => 'Data Admin'
        ];

        $activeMenu = 'admin'; // menu yang sedang aktif

        return view('admin.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $admin = AdminModel::select('admin_id', 'nama', 'email', 'telp');

            return DataTables::of($admin)
                ->addIndexColumn()
                ->addColumn('aksi', function ($adm) {
                    $btn  = '<div class="btn-group" role="group">';
                    $btn .= '<button onclick="modalAction(\''.url('/admin/' . $adm->admin_id . '/show_ajax').'\')" class="btn btn-primary btn-sm" style="margin-right: 5px;" title="Detail Data">';
                    $btn .= '<i class="fas fa-info-circle"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/admin/' . $adm->admin_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm" style="margin-right: 5px;" title="Edit Data">';
                    $btn .= '<i class="fas fa-edit"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/admin/' . $adm->admin_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm" title="Hapus Data">';
                    $btn .= '<i class="fas fa-trash-alt"></i></button>';
                    $btn .= '</div>';

                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function create_ajax()
    {
        return view('admin.create_ajax'); // Pastikan view ini tersedia
    }

    public function store_ajax(Request $request)
    {
        try {
            // Validasi data untuk tabel m_users
            $validatedUser = $request->validate([
                'username' => 'required|unique:m_users,username',
                'password' => 'required|min:5',
            ]);

            // Simpan ke tabel m_users
            $user = UserModel::create([
                'username' => $validatedUser['username'],
                'password' => bcrypt($validatedUser['password']),
                'level_id' => 1, // level 1 untuk admin
            ]);

            // Validasi data untuk tabel m_admin
            $validatedAdmin = $request->validate([
                'nama' => 'required|max:100',
                'email' => 'required|email|unique:m_admin,email',
                'telp' => 'nullable|max:20',
            ]);

            // Simpan ke tabel m_admin
            AdminModel::create([
                'user_id' => $user->user_id,
                'nama' => $validatedAdmin['nama'],
                'email' => $validatedAdmin['email'],
                'telp' => $validatedAdmin['telp'] ?? null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data admin berhasil disimpan'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'msgField' => $e->errors(),
                'message' => 'Validasi gagal, periksa inputan Anda'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function confirm_ajax($admin_id)
    {
        $admin = AdminModel::where('admin_id', $admin_id)->first();

        return view('admin.confirm_ajax', compact('admin'));
    }

    public function delete_ajax(Request $request, $admin_id)
    {
        $admin = AdminModel::where('admin_id', $admin_id)->first();

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Data admin tidak ditemukan.'
            ], 404);
        }

        // Simpan user_id sebelum admin dihapus
        $userId = $admin->user_id;

        // Hapus admin
        $admin->delete();

        // Hapus user terkait
        UserModel::where('user_id', $userId)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data admin berhasil dihapus.'
        ]);
    }

    public function show_ajax($admin_id)
    {
        // Ambil data admin beserta relasi user-nya
        $admin = AdminModel::with('user')->where('admin_id', $admin_id)->first();

        // Jika data tidak ditemukan
        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Data admin dengan ID ' . $admin_id . ' tidak ditemukan.'
            ], 404);
        }

        // Tampilkan view show_ajax untuk admin
        return view('admin.show_ajax', [
            'admin' => $admin
        ]);
    }

    public function edit_ajax($admin_id)
    {
        // Ambil data admin beserta user-nya
        $admin = AdminModel::with('user')->find($admin_id);

        return view('admin.edit_ajax', compact('admin'));
    }

    public function update_ajax(Request $request, $admin_id)
    {
        $admin = AdminModel::with('user')->find($admin_id);

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Data admin tidak ditemukan.'
            ]);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'username'  => 'required|max:20|unique:m_users,username,' . $admin->user->user_id . ',user_id',
            'password'  => 'nullable|min:5|max:20',
            'nama'      => 'required|max:100',
            'email'     => 'required|email|max:100',
            'telp'      => 'nullable|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal, periksa input anda.',
                'msgField' => $validator->errors()
            ]);
        }

        try {
            // Update data admin
            $admin->nama = $request->nama;
            $admin->email = $request->email;
            $admin->telp = $request->telp;
            $admin->save();

            // Update user
            $user = $admin->user;
            $user->username = $request->username;
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Data admin berhasil diperbarui.'
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
        $admin = DB::table('m_admin')
            ->join('m_users', 'm_admin.user_id', '=', 'm_users.user_id')
            ->join('r_auth_level', 'm_users.level_id', '=', 'r_auth_level.level_id')
            ->select('m_users.username', 'm_admin.nama', 'r_auth_level.level_name')
            ->orderBy('m_admin.admin_id', 'asc')
            ->get();

        $pdf = Pdf::loadView('admin.export_pdf', ['admin' => $admin]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data Admin ' . date('Y-m-d H:i:s') . '.pdf');
    }

    public function export_excel()
    {
        // Ambil data admin dengan relasi user dan level
        $admin = AdminModel::select('nama', 'user_id')
            ->with(['user' => function ($query) {
                $query->select('user_id', 'username', 'level_id');
            }, 'user.level']) // Pastikan relasi user() dan level() ada di modelnya
            ->orderBy('admin_id')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Level');

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;

        foreach ($admin as $data) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $data->user->username ?? '-');
            $sheet->setCellValue('C' . $baris, $data->nama);
            $sheet->setCellValue('D' . $baris, $data->user->level->level_name ?? '-');
            $no++;
            $baris++;
        }

        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Admin');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Admin_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function import()
    {
        return view('admin.import'); // Pastikan view-nya tersedia di resources/views/admin/import.blade.php
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_admin' => ['required', 'mimes:xlsx', 'max:1024']
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
                $file = $request->file('file_admin');
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, false, true, true);

                $insertedCount = 0;
                $existingUsernames = UserModel::pluck('username')->toArray();
                $existingEmails = AdminModel::pluck('email')->toArray();

                if (count($data) > 1) {
                    foreach ($data as $index => $row) {
                        if ($index <= 1) continue; // Skip header

                        $username = trim($row['A']);
                        $password = trim($row['B']);
                        $nama     = trim($row['C']);
                        $email    = trim($row['D']);
                        $telp     = trim($row['E'] ?? '');

                        if (!$username || !$password || !$nama || !$email) continue;
                        if (in_array($username, $existingUsernames) || in_array($email, $existingEmails)) continue;

                        $user = UserModel::create([
                            'username' => $username,
                            'password' => bcrypt($password),
                            'level_id' => 1, // level 1 = admin
                            'created_at' => now()
                        ]);

                        AdminModel::create([
                            'user_id' => $user->user_id,
                            'nama' => $nama,
                            'email' => $email,
                            'telp' => $telp ?: null,
                            'created_at' => now()
                        ]);

                        $insertedCount++;
                    }

                    return response()->json([
                        'status' => true,
                        'message' => "$insertedCount admin berhasil diimport"
                    ]);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data admin yang diimport'
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

    public function show_admin($admin_id)
    {
        // Ambil data admin beserta relasi user-nya
        $admin = AdminModel::with('user')->where('admin_id', $admin_id)->first();

        // Jika data tidak ditemukan
        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Data admin dengan ID ' . $admin_id . ' tidak ditemukan.'
            ], 404);
        }

        // Tampilkan view show_ajax untuk admin
        return view('admin.show_admin', [
            'admin' => $admin
        ]);
    }

    public function edit_admin($admin_id)
    {
        $admin = AdminModel::with('user')->find($admin_id);

        if (!$admin) {
            abort(404, 'Data admin tidak ditemukan.');
        }

        return view('admin.edit_admin', compact('admin'));
    }

    public function update_admin(Request $request, $admin_id)
    {
        $admin = AdminModel::with('user')->find($admin_id);

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Data admin tidak ditemukan.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|max:20|unique:m_users,username,' . $admin->user->user_id . ',user_id',
            'password' => 'nullable|min:5|max:20',
            'nama'     => 'required|max:100',
            'email'    => 'required|email|max:100',
            'telp'     => 'nullable|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal, periksa input anda.',
                'msgField' => $validator->errors()
            ]);
        }

        try {
            // Simpan data admin
            $admin->nama = $request->nama;
            $admin->email = $request->email;
            $admin->telp = $request->telp;

            if ($request->hasFile('profile_picture')) {
                if ($admin->profile_picture && Storage::disk('public')->exists($admin->profile_picture)) {
                    Storage::disk('public')->delete($admin->profile_picture);
                }

                $path = $request->file('profile_picture')->store('profile_admin', 'public');
                $admin->profile_picture = $path;
            }

            $admin->save();

            // Update user
            $user = $admin->user;
            $user->username = $request->username;
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Data admin berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function hapus_foto_profile($admin_id)
    {
        $admin = AdminModel::find($admin_id);

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Data admin tidak ditemukan.'
            ]);
        }

        try {
            if ($admin->profile_picture && Storage::disk('public')->exists($admin->profile_picture)) {
                Storage::disk('public')->delete($admin->profile_picture);
            }

            $admin->profile_picture = null;
            $admin->save();

            return response()->json([
                'status' => true,
                'message' => 'Foto profil admin berhasil dihapus.'
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
