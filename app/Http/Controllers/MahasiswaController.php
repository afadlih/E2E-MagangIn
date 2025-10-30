<?php

namespace App\Http\Controllers;

use App\Models\BidangKeahlianModel;
use App\Models\MahasiswaModel;
use App\Models\UserModel;
use App\Models\ProdiModel;
use App\Models\LevelModel;
use App\Models\DosenModel;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Storage;
use App\Models\NegaraModel;
use App\Models\PrefrensiLokasiMahasiswaModel;
use App\Models\ProvinsiModel;
use App\Models\KabupatenModel;
use App\Models\SkillModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MahasiswaController extends Controller
{
    // Menampilkan halaman awal mahasiswa
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Mahasiswa',
            'list'  => ['Home', 'Mahasiswa']
        ];

        $page = (object) [
            'title' => 'Data Mahasiswa'
        ];

        $activeMenu = 'mahasiswa'; // set menu yang sedang aktif

        $prodis = ProdiModel::all(); // ambil data prodi untuk filter

        return view('mahasiswa.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'prodis' => $prodis, // kirim data prodi ke view
        ]);
    }


    public function list(Request $request)
    {
        if ($request->ajax()) {
            $mahasiswa = MahasiswaModel::with('prodi')
                ->select('mhs_nim as nim', 'full_name as nama', 'prodi_id', 'user_id', 'angkatan', 'jenis_kelamin')
                ->orderBy('user_id', 'asc');

            // Filter berdasarkan prodi jika ada input filter
            if ($request->prodi_id) {
                $mahasiswa->where('prodi_id', $request->prodi_id);
            }

            return DataTables::of($mahasiswa)
                ->addIndexColumn()
                ->addColumn('prodi', function ($mhs) {
                    return $mhs->prodi ? $mhs->prodi->nama_prodi : '-';
                })
                ->addColumn('aksi', function ($mhs) {
                    $btn  = '<div class="btn-group" role="group">';
                    $btn .= '<button onclick="modalAction(\''.url('/mahasiswa/' . $mhs->nim . '/show_ajax').'\')" class="btn btn-primary btn-sm" style="margin-right: 5px;" title="Detail Data">';
                    $btn .= '<i class="fas fa-info-circle"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/mahasiswa/' . $mhs->nim . '/edit_ajax').'\')" class="btn btn-warning btn-sm" style="margin-right: 5px;" title="Edit Data">';
                    $btn .= '<i class="fas fa-edit"></i></button>';
                    $btn .= '<button onclick="modalAction(\''.url('/mahasiswa/' . $mhs->nim . '/delete_ajax').'\')" class="btn btn-danger btn-sm" title="Hapus Data">';
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
        $prodis = ProdiModel::all(); // ambil semua program studi

        return view('mahasiswa.create_ajax', compact('prodis'));
    }



    public function store_ajax(Request $request)
    {
       $validated = $request->validate([
            'username' => 'required|unique:m_users,username',
            'password' => 'required',
            'mhs_nim' => 'required|unique:m_mahasiswa,mhs_nim',
            'full_name' => 'required',
            'alamat' => 'nullable',
            'telp' => 'nullable',
            'prodi_id' => 'required',
            'angkatan' => 'nullable|integer',
            'jenis_kelamin' => 'nullable|in:L,P',
            'ipk' => 'nullable|numeric|min:0|max:4.0',
            'status_magang' => 'required',
        ]);

        // Setelah semua validasi berhasil, baru simpan ke database

        $user = UserModel::create([
            'username' => $validated['username'],
            'password' => bcrypt($validated['password']),
            'level_id' => 3,
        ]);

        MahasiswaModel::create([
            'user_id' => $user->user_id,
            'mhs_nim' => $validated['mhs_nim'],
            'full_name' => $validated['full_name'],
            'alamat' => $validated['alamat'] ?? null,
            'telp' => $validated['telp'] ?? null,
            'prodi_id' => $validated['prodi_id'],
            'angkatan' => $validated['angkatan'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'ipk' => $validated['ipk'] ?? null,
            'status_magang' => $validated['status_magang'],
            'created_at' => now()
        ])->save();
        // Jika ada file CV, simpan di storage
        if ($request->hasFile('file_cv')) {
            $path = $request->file('file_cv')->store('public/cv');
            // Simpan path file CV ke database
            MahasiswaModel::where('mhs_nim', $validated['mhs_nim'])->update(['file_cv' => str_replace('public/', '', $path)]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data Mahasiswa Berhasil Ditambahkan'
        ]);
    }


    public function confirm_ajax($nim)
    {
        $mahasiswa = MahasiswaModel::with('prodi')->where('mhs_nim', $nim)->first();

        return view('mahasiswa.confirm_ajax', compact('mahasiswa'));
    }

    public function delete_ajax(Request $request, $nim)
    {
        $mahasiswa = MahasiswaModel::where('mhs_nim', $nim)->first();

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ], 404);
        }

        // Simpan ID user dulu sebelum hapus mahasiswa
        $userId = $mahasiswa->user_id;

        // Hapus mahasiswa terlebih dahulu
        $mahasiswa->delete();

        // Setelah itu baru hapus user terkait
        UserModel::where('user_id', $userId)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data mahasiswa berhasil dihapus.'
        ]);
    }

     public function show_ajax($mhs_nim)
    {
        $mahasiswa = MahasiswaModel::with([
            'user',
            'prodi',
            'provinsi',
            'kabupaten',
            'bidangKeahlian',
            'skills',
        ])->where('mhs_nim', $mhs_nim)->first();


        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa dengan NIM ' . $mhs_nim . ' tidak ditemukan.'
            ], 404);
        }

        return view('mahasiswa.show_ajax', [
            'mahasiswa' => $mahasiswa
        ]);
    }


    public function edit_ajax($mhs_nim)
    {
        $mahasiswa = MahasiswaModel::with(['prodi', 'user', 'provinsi', 'kabupaten', 'skills'])->find($mhs_nim);
        $prodiList = ProdiModel::all();
        $bidangKeahlian = BidangKeahlianModel::all(); // Tambahkan baris ini
        $negaraList = NegaraModel::all();
        $provinsiList = ProvinsiModel::all();
        $allSkills    = SkillModel::all(); 
        $kabupatenList = $mahasiswa->kabupaten_id
            ? KabupatenModel::where('provinsi_id', $mahasiswa->provinsi_id)->get()
            : KabupatenModel::all();


        return view('mahasiswa.edit_ajax', compact(
            'mahasiswa',
            'prodiList',
            'bidangKeahlian',
            'negaraList',
            'provinsiList',
            'kabupatenList',
            'allSkills',
        ));

    }


    public function update_ajax(Request $request, $old_nim)
{
    $mahasiswa = MahasiswaModel::with('user')->find($old_nim);

    if (!$mahasiswa) {
        return response()->json([
            'status' => false,
            'message' => 'Data mahasiswa tidak ditemukan.'
        ]);
    }

    // Validasi input
    $validator = Validator::make($request->all(), [
        'username'      => 'required|max:20|unique:m_users,username,' . $mahasiswa->user->user_id . ',user_id',
        'password'      => 'nullable|min:5|max:20',
        'full_name'     => 'required|max:100',
        'alamat'        => 'nullable|max:255',
        'telp'          => 'nullable|max:20',
        'angkatan'      => 'nullable|integer',
        'jenis_kelamin' => 'nullable|in:L,P',
        'ipk'           => 'nullable|numeric|min:0|max:4.0',
        'file_cv'       => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        'skills'   => 'nullable|array',
        'skills.*' => 'integer|exists:skills,id',
       // 'durasi'   => 'required|in:3,6',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validasi gagal, periksa input anda.',
            'msgField' => $validator->errors()
        ]);
    }

    try {
        DB::beginTransaction();

        // Simpan data mahasiswa
        $newNim = $request->mhs_nim;
        $fileCV = $mahasiswa->file_cv;

        if ($request->hasFile('file_cv')) {
            if ($fileCV && Storage::exists('public/' . $fileCV)) {
                Storage::delete('public/' . $fileCV);
            }
            $path = $request->file('file_cv')->store('public/cv');
            $fileCV = str_replace('public/', '', $path);
        }

        // Update data mahasiswa (ganti primary key jika NIM berubah)
        $mahasiswa->full_name = $request->full_name;
        $mahasiswa->alamat = $request->alamat;
        $mahasiswa->telp = $request->telp;
        $mahasiswa->angkatan = $request->angkatan;
        $mahasiswa->jenis_kelamin = $request->jenis_kelamin;
        $mahasiswa->ipk = $request->ipk;
        $mahasiswa->file_cv = $fileCV;
        $mahasiswa->durasi = $request->durasi;
        $mahasiswa->skills()->sync($request->input('skills', []));
        $mahasiswa->save();
        $mahasiswa->bidang_keahlian_id = $request->bidang_keahlian_id; // Update bidang keahlian

        

        // Update data user
        $user = $mahasiswa->user;
        $user->username = $request->username;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Data mahasiswa berhasil diperbarui.'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Terjadi kesalahan saat menyimpan data.',
            'error' => $e->getMessage()
        ]);
    }
}

   public function export_pdf()
    {
        $mahasiswa = DB::table('m_mahasiswa')
            ->join('m_users', 'm_mahasiswa.user_id', '=', 'm_users.user_id')
            ->join('r_auth_level', 'm_users.level_id', '=', 'r_auth_level.level_id')
            ->leftJoin('m_program_studi', 'm_mahasiswa.prodi_id', '=', 'm_program_studi.prodi_id')
            ->select(
                'm_users.username',
                'm_mahasiswa.full_name',
                'm_mahasiswa.telp',
                'm_program_studi.nama_prodi as program_studi',
                'm_mahasiswa.angkatan',
                'm_mahasiswa.jenis_kelamin',
                'm_mahasiswa.ipk',
                'm_mahasiswa.alamat',
                'm_mahasiswa.status_magang',
                'r_auth_level.level_name'
            )
            ->orderBy('m_mahasiswa.mhs_nim', 'asc')
            ->get();

        $pdf = Pdf::loadView('mahasiswa.export_pdf', ['mahasiswa' => $mahasiswa]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data Mahasiswa ' . date('Y-m-d H:i:s') . '.pdf');
    }

    public function export_excel()
    {
        $mahasiswa = MahasiswaModel::with(['user', 'user.level', 'prodi'])
            ->orderBy('mhs_nim')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'No. Telepon');
        $sheet->setCellValue('E1', 'Program Studi');
        $sheet->setCellValue('F1', 'Angkatan');
        $sheet->setCellValue('G1', 'Jenis Kelamin');
        $sheet->setCellValue('H1', 'IPK');
        $sheet->setCellValue('I1', 'Alamat');
        $sheet->setCellValue('J1', 'Status Magang');
        $sheet->setCellValue('K1', 'Level');

        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;

        foreach ($mahasiswa as $data) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $data->user->username ?? '-');
            $sheet->setCellValue('C' . $baris, $data->full_name);
            $sheet->setCellValue('D' . $baris, $data->telp ?? '-');
            $sheet->setCellValue('E' . $baris, $data->prodi->nama_prodi ?? '-');
            $sheet->setCellValue('F' . $baris, $data->angkatan ?? '-');
            $sheet->setCellValue('G' . $baris, $data->jenis_kelamin == 'L' ? 'Laki-laki' : ($data->jenis_kelamin == 'P' ? 'Perempuan' : '-'));
            $sheet->setCellValue('H' . $baris, $data->ipk ?? '-');
            $sheet->setCellValue('I' . $baris, $data->alamat ?? '-');
            $sheet->setCellValue('J' . $baris, $data->status_magang ?? '-');
            $sheet->setCellValue('K' . $baris, $data->user->level->level_name ?? '-');
            $no++;
            $baris++;
        }

        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Mahasiswa');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Mahasiswa_' . date('Y-m-d_H-i-s') . '.xlsx';

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
        return view('mahasiswa.import'); // Pastikan view-nya benar
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // Validasi file Excel
                'file_mahasiswa' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_mahasiswa');
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insertedCount = 0;
            $existingUsernames = UserModel::pluck('username')->toArray();
            $existingNIMs = MahasiswaModel::pluck('mhs_nim')->toArray();

            if (count($data) > 1) {
                foreach ($data as $baris => $row) {
                    if ($baris <= 1) continue; // Skip header

                    $username = trim($row['A']);
                    $password = trim($row['B']);
                    $mhs_nim  = trim($row['C']);
                    $nama     = trim($row['D']);
                    $alamat   = trim($row['E'] ?? '');
                    $telp     = trim($row['F'] ?? '');
                    $prodi_id = trim($row['G']);
                    $angkatan = trim($row['H'] ?? '');
                    $jenis_kelamin = trim($row['I'] ?? '');
                    $ipk = trim($row['J'] ?? '');

                    if (!$username || !$password || !$mhs_nim || !$nama || !$prodi_id) continue;
                    if (in_array($username, $existingUsernames) || in_array($mhs_nim, $existingNIMs)) continue;

                    $user = UserModel::create([
                        'username' => $username,
                        'password' => bcrypt($password),
                        'level_id' => 3,
                        'created_at' => now()
                    ]);

                    MahasiswaModel::create([
                        'user_id' => $user->user_id,
                        'mhs_nim' => $mhs_nim,
                        'full_name' => $nama,
                        'alamat' => $alamat ?: null,
                        'telp' => $telp ?: null,
                        'prodi_id' => $prodi_id,
                       'angkatan' => $angkatan, // pastikan ini ada
                        'jenis_kelamin' => $jenis_kelamin,
                        'ipk' => $ipk,
                        'status_magang' => 'belum magang',
                        'created_at' => now()
                    ]);

                    $insertedCount++;
                }

                return response()->json([
                    'status' => true,
                    'message' => "$insertedCount mahasiswa berhasil diimport"
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Tidak ada data yang diimport'
            ]);
        }

        return redirect('/');
    }

    public function show_mhs($mhs_nim)
    {
        $mahasiswa = MahasiswaModel::with([
            'user',
            'prodi',
            'provinsi',
            'kabupaten',
            'bidangKeahlian',
            'skills',
        ])->where('mhs_nim', $mhs_nim)->first();


        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa dengan NIM ' . $mhs_nim . ' tidak ditemukan.'
            ], 404);
        }

        return view('mahasiswa.show_mhs', [
            'mahasiswa' => $mahasiswa
        ]);
    }


    public function edit_mhs($mhs_nim)
    {
        $mahasiswa = MahasiswaModel::with(['prodi', 'user', 'provinsi', 'kabupaten', 'skills'])->find($mhs_nim);
        $bidangKeahlian = BidangKeahlianModel::all();
        $negara = NegaraModel::all();
        $provinsi = ProvinsiModel::all();
        $allSkills      = SkillModel::all();             // ← add this
        $durasiList = [3 => '3 Bulan', 6 => '6 Bulan'];
        $kabupaten = $mahasiswa->kabupaten_id
            ? KabupatenModel::where('provinsi_id', $mahasiswa->provinsi_id)->get()
            : KabupatenModel::all();
        $tipe_bekerjaList = [
            'remote' => 'Remote',
            'o_nsite' => 'On_site',
            'hybrid' => 'Hybrid'
        ];
        return view(
            'mahasiswa.edit_mhs',
            [
                'mahasiswa' => $mahasiswa,
                'bidangKeahlian' => $bidangKeahlian,
                'provinsiList' => $provinsi,
                'kabupatenList' => $kabupaten,
                'negaraList' => $negara,
                'allSkills' => $allSkills, // ← add this
                'durasiList' => $durasiList, // ← add this
                'tipe_bekerjaList' => $tipe_bekerjaList, // ← add this
                
            ]
        );
    }


    public function update_mhs(Request $request, $mhs_nim)
    {
        $mahasiswa = MahasiswaModel::with('user')->find($mhs_nim);

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|max:20|unique:m_users,username,' . $mahasiswa->user->user_id . ',user_id',
            'password' => 'nullable|min:5|max:20',
            'full_name' => 'required|max:100',
            'alamat' => 'nullable|max:255',
            'telp' => 'nullable|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'angkatan' => 'nullable|integer',
            'jenis_kelamin' => 'nullable|in:L,P',
            'ipk' => 'nullable|numeric|min:0|max:4.0',
            'file_cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'bidang_keahlian_id' => 'required|array',
            'bidang_keahlian_id.*' => 'exists:m_bidang_keahlian,id',
            'negara_id' => 'nullable|exists:m_negara,id',
            'provinsi_id' => 'nullable|exists:m_provinsi,id',
            'kabupaten_id' => 'nullable|exists:m_kabupaten,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'durasi' => 'required|in:3,6', // Periode magang (3 atau 6 bulan)
            'skills'   => 'nullable|array',
            'skills.*' => 'integer|exists:skills,id', // Validasi untuk skills
            'tipe_bekerja' => 'nullable|in:remote,on_site,hybrid', // Validasi tipe bekerja

        ]);

        $mahasiswa->lokasi = $request->provinsi_id;
        $mahasiswa->save();

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal, periksa input anda.',
                'msgField' => $validator->errors()
            ]);
        }

        try {
            // Update file CV
            if ($request->hasFile('file_cv')) {
                if ($mahasiswa->file_cv && Storage::exists('public/' . $mahasiswa->file_cv)) {
                    Storage::delete('public/' . $mahasiswa->file_cv);
                }
                $path = $request->file('file_cv')->store('public/cv');
                $mahasiswa->file_cv = str_replace('public/', '', $path);
            }

            // Update foto profil
            if ($request->hasFile('profile_picture')) {
                if ($mahasiswa->profile_picture && Storage::disk('public')->exists($mahasiswa->profile_picture)) {
                    Storage::disk('public')->delete($mahasiswa->profile_picture);
                }
                $mahasiswa->profile_picture = $request->file('profile_picture')->store('profile_mahasiswa', 'public');
            }

            // Update data mahasiswa
            $mahasiswa->fill([
                'full_name' => $request->full_name,
                'alamat' => $request->alamat,
                'telp' => $request->telp,
                'angkatan' => $request->angkatan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'ipk' => $request->ipk,
                'provinsi_id' => $request->provinsi_id,
                'kabupaten_id' => $request->kabupaten_id,
                'negara_id' => $request->negara_id,
                'durasi' => $request->durasi, // Periode magang
                'skills' => $request->input('skills', []), // Update skills
                'tipe_bekerja' => $request->tipe_bekerja, // Tipe bekerja
            ]);
            $mahasiswa->save();

            // Ambil nama-nama wilayah
            // Ambil nama-nama wilayah dari model
            $negara = NegaraModel::find($request->negara_id)?->nama ?? 'Negara Tidak Diketahui';
            $provinsi = ProvinsiModel::find($request->provinsi_id)?->nama ?? null;
            $kabupaten = KabupatenModel::find($request->kabupaten_id)?->nama ?? null;

            // Susun nama tampilan lokasi
            $namaTampilan = $negara;
            if ($provinsi) {
                $namaTampilan .= ', ' . $provinsi;
            }
            if ($kabupaten) {
                $namaTampilan .= ', ' . $kabupaten;
            }

            // Simpan atau update lokasi preferensi mahasiswa
            PrefrensiLokasiMahasiswaModel::updateOrCreate(
                ['mhs_nim' => $mahasiswa->mhs_nim],
                [
                    'provinsi_id' => $request->provinsi_id,
                    'kabupaten_id' => $request->kabupaten_id,
                    'negara_id' => $request->negara_id,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'nama_tampilan' => $namaTampilan,
                ]
            );

            // Update skills mahasiswa
            if ($request->has('skills')) {
                $mahasiswa->skills()->sync($request->input('skills', []));
            } else {
                $mahasiswa->skills()->detach();
            }

            // Update tipe bekerja
            if ($request->has('tipe_bekerja')) {
                $mahasiswa->tipe_bekerja = $request->tipe_bekerja;
            } else {
                $mahasiswa->tipe_bekerja = null; // Atau nilai default lainnya
            }
            $mahasiswa->save();


            // Update bidang keahlian (relasi many-to-many)
            $mahasiswa->bidangKeahlian()->sync($request->bidang_keahlian_id);

            $names = BidangKeahlianModel::whereIn('id', $request->bidang_keahlian_id)
                        ->pluck('nama')           // get the “nama” column
                        ->toArray();              // [ 'Java', 'PHP', … ]

            $mahasiswa->pref = implode(', ', $names);  // "Java, PHP, C++"
            $mahasiswa->save();

            // Update akun user
            $user = $mahasiswa->user;
            $user->username = $request->username;
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Data mahasiswa berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage()
            ]);
        }
    }





    public function hapus_foto_profile($mhs_nim)
    {
        $mahasiswa = MahasiswaModel::find($mhs_nim);

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ]);
        }

        try {
            if ($mahasiswa->profile_picture && Storage::disk('public')->exists($mahasiswa->profile_picture)) {
                Storage::disk('public')->delete($mahasiswa->profile_picture);
            }

            $mahasiswa->profile_picture = null;
            $mahasiswa->save();

            return response()->json([
                'status' => true,
                'message' => 'Foto profil mahasiswa berhasil dihapus.'
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