<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogAktivitasMhsModel;
use App\Models\KomenLogAktivitasModel;
use App\Models\MahasiswaModel;
use App\Models\LamaranMagangModel;
use App\Models\DosenModel;
use App\Models\LowonganModel;
use App\Models\ProdiModel;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class LogAktivitasMhsController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Log Aktivitas Mahasiswa',
            'list'  => ['Home', 'Log Aktivitas']
        ];

        $page = (object) [
            'title' => 'Data Log Aktivitas Mahasiswa'
        ];

        $activeMenu = 'log_aktivitas';

        // Tambahkan Auth::user()
        $user = Auth::user();

        // Ambil data dosen berdasarkan user_id
        $dosen = DosenModel::where('user_id', $user->user_id)->first();

        if (!$dosen) {
            return redirect()->back()->withErrors('Dosen tidak ditemukan.');
        }

        $prodis = ProdiModel::all();

        // Ambil mahasiswa yang memiliki lamaran dengan dosen tersebut
        $mahasiswas = MahasiswaModel::whereIn('mhs_nim', function ($query) use ($dosen) {
            $query->select('mhs_nim')
                ->from('t_lamaran_magang')
                ->where('dosen_id', $dosen->dosen_id);
        })->get();

        return view('log_aktivitas.index', compact('breadcrumb', 'page', 'activeMenu', 'prodis', 'mahasiswas'));
    }


    public function list(Request $request)
    {
            $user = Auth::user();
            $dosen = DosenModel::where('user_id', $user->user_id)->first();

            if (!$dosen) {
                return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
            }

            $dosenId = $dosen->dosen_id;

            // Ambil daftar mhs_nim dari lamaran dosen ini
            $mahasiswa = LamaranMagangModel::where('dosen_id', $dosenId)
                ->pluck('mhs_nim')
                ->unique()
                ->values();

            if ($mahasiswa->isEmpty()) {
                return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
            }

            $aktivitas = LogAktivitasMhsModel::with(['komentar', 'lamaran.mahasiswa.prodi'])
                ->whereHas('lamaran', function ($query) use ($mahasiswa) {
                    $query->whereIn('mhs_nim', $mahasiswa);
                });

            if ($request->filled('prodi_id')) {
                $aktivitas->whereHas('lamaran.mahasiswa', function ($query) use ($request) {
                    $query->where('prodi_id', $request->prodi_id);
                });
            }

            if ($request->filled('mhs_nim')) {
                $aktivitas->whereHas('lamaran', function ($query) use ($request) {
                    $query->where('mhs_nim', $request->mhs_nim);
                });
            }

            $aktivitas = $aktivitas->orderByDesc('waktu');

            return DataTables::of($aktivitas)
            ->addIndexColumn()
            ->addColumn('nama', fn($item) => optional($item->lamaran->mahasiswa)->full_name ?? '-')
            ->addColumn('prodi', fn($item) => optional(optional($item->lamaran->mahasiswa)->prodi)->nama_prodi ?? '-')
            ->addColumn('keterangan', fn($item) => $item->keterangan)
            ->addColumn('waktu', fn($item) => $item->waktu->format('d-m-Y'))
            ->addColumn('aksi', function ($item) {
                return '<button onclick="modalAction(\'' . url('/log-aktivitas/' . $item->aktivitas_id . '/show_ajax') . '\')" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></button>';
            })
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->search['value'] != '') {
                    $search = $request->search['value'];
                    $query->where(function($q) use ($search) {
                        $q->whereHas('lamaran.mahasiswa', function($mahasiswaQuery) use ($search) {
                            $mahasiswaQuery->where('full_name', 'like', "%{$search}%");
                        })->orWhereHas('lamaran.mahasiswa.prodi', function($prodiQuery) use ($search) {
                            $prodiQuery->where('nama_prodi', 'like', "%{$search}%");
                        })->orWhere('keterangan', 'like', "%{$search}%");
                    });
                }
            })
            ->rawColumns(['aksi'])
            ->make(true);

        return abort(404);
    }

    public function showAjax($id)
    {
        $aktivitas = LogAktivitasMhsModel::with([
            'lamaran.mahasiswa.prodi',
            'lamaran.lowongan.perusahaan'
        ])->findOrFail($id);

        $komentar = KomenLogAktivitasModel::where('aktivitas_id', $id)
            ->with('dosen')
            ->latest('created_at')
            ->get();

        return view('log_aktivitas.show_ajax', compact('aktivitas', 'komentar'));
    }


    public function storeKomentar(Request $request, $id)
    {
        $request->validate([
            'komentar' => 'required|string|max:1000',
        ]);

        try {
            $user = Auth::user();
            $dosen = DosenModel::where('user_id', $user->user_id)->firstOrFail();

            KomenLogAktivitasModel::create([
                'aktivitas_id' => $id,
                'dosen_id'     => $dosen->dosen_id,
                'komentar'     => $request->komentar,
                'created_at'   => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'message' => 'Feedback/Saran berhasil ditambahkan.']);
    }

    public function index_mhs()
    {
        $breadcrumb = (object) [
            'title' => 'Log Aktivitas Mahasiswa',
            'list'  => ['Home', 'Log Aktivitas']
        ];

        $page = (object) [
            'title' => 'Data Log Aktivitas Mahasiswa'
        ];

        $activeMenu = 'log_aktivitas';

        $user = Auth::user();
        $mahasiswa = MahasiswaModel::where('user_id', $user->user_id)->first();

        if (!$mahasiswa) {
            return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $mhsNim = $mahasiswa->mhs_nim;

        // Ambil satu lamaran_id milik mahasiswa
        $lamaran = LamaranMagangModel::where('mhs_nim', $mhsNim)
            ->where('status', 'diterima')
            ->first();

        $lamaranSelesai = LamaranMagangModel::where('mhs_nim', $mhsNim)
            ->where('status', 'selesai')
            ->first();
        if ($lamaranSelesai) {
            return view('log_aktivitas_mhs.index', compact('breadcrumb', 'page', 'activeMenu', 'lamaranSelesai'));

        }elseif (!$lamaran && !$lamaranSelesai) {
            return view('log_aktivitas_mhs.index', compact('breadcrumb', 'page', 'activeMenu'));
        }

        $lamaranId = $lamaran->lamaran_id;

        return view('log_aktivitas_mhs.index', compact('breadcrumb', 'page', 'activeMenu', 'lamaranId'));
    }
    //user mahasiswa
    public function list_pov_mhs(Request $request)
    {
        $user = auth()->user()->mahasiswa->mhs_nim;
        $mahasiswa = MahasiswaModel::where('mhs_nim', $user)->first();

        if (!$mahasiswa) {
            return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $mhsNim = $mahasiswa->mhs_nim;

        // Ambil daftar aktivitas berdasarkan mhs_nim dari mahasiswa yang login
        $aktivitas = LogAktivitasMhsModel::with(['lamaran'])
            ->whereHas('lamaran', function ($query) use ($mhsNim) {
                $query->where('mhs_nim', $mhsNim)
                    ->where(function($q) {
                        $q->where('status', 'diterima')
                            ->orWhere('status', 'selesai');
                    });
            })
            ->orderBy('waktu');


        return DataTables::of($aktivitas)
            ->addIndexColumn()
            ->addColumn('aksi', function ($item) {
                return '<button onclick="modalAction(\'' . url('/log-aktivitas-mhs/' . $item->aktivitas_id) . '/edit_ajax'. '\')" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Edit</button> ' .
                    '<button onclick="modalAction(\'' . url('/log-aktivitas-mhs/' . $item->aktivitas_id . '/show_ajax') . '\')" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></button>';
            })
            ->rawColumns(['aksi'])
            ->make(true);

        return abort(404);
    }

    public function showAjaxMhs($id)
    {
        $aktivitas = LogAktivitasMhsModel::with(['lamaran.mahasiswa.prodi'])->findOrFail($id);
        $komentar = KomenLogAktivitasModel::where('aktivitas_id', $id)
            ->with('dosen')
            ->latest('created_at')
            ->get();

        return view('log_aktivitas_mhs.show_ajax', compact('aktivitas', 'komentar'));
    }

    public function create_ajax($lamaran_id)
    {
        return view('log_aktivitas_mhs.create_ajax', compact('lamaran_id'));
    }

    public function store_ajax(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'lamaran_id' => 'required|integer|exists:t_lamaran_magang,lamaran_id', // Ensure lamaran_id exists in the 'lamaran' table
            'waktu' => 'required|date', // Ensure waktu is a valid date
            'keterangan' => 'required|string|max:255', // Keterangan is required, max 255 characters
        ], [
            'lamaran_id.required' => 'Lamaran ID wajib diisi.',
            'lamaran_id.integer' => 'Lamaran ID harus berupa angka.',
            'lamaran_id.exists' => 'Lamaran ID tidak ditemukan.',
            'waktu.required' => 'Tanggal wajib diisi.',
            'waktu.date' => 'Tanggal tidak valid.',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'keterangan.max' => 'Keterangan tidak boleh lebih dari 255 karakter.',
        ]);

        // Check for duplicate waktu for the same lamaran_id
        $existingLog = LogAktivitasMhsModel::where('lamaran_id', $validatedData['lamaran_id'])
            ->whereDate('waktu', $validatedData['waktu'])
            ->first();

        if ($existingLog) {
            return response()->json([
                'status' => false,
                'message' => 'Anda sudah mengisi log aktiviitas hari ini.',
                'msgField' => ['waktu' => 'Log hari ini sudah terisi.']
            ], 422);
        }

        // Create the new log aktivitas entry
        LogAktivitasMhsModel::create([
            'lamaran_id' => $validatedData['lamaran_id'],
            'waktu' => $validatedData['waktu'],
            'keterangan' => $validatedData['keterangan'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data log aktivitas berhasil disimpan'
        ]);
    }

    public function edit_ajax($aktivitas_id)
    {
        $aktivitas = LogAktivitasMhsModel::select()->where('aktivitas_id', $aktivitas_id)->first();
        return view('log_aktivitas_mhs.edit_ajax', compact('aktivitas'));
    }

    public function update_ajax(Request $request, $aktivitas_id)
    {
        $aktivitas = LogAktivitasMhsModel::where('aktivitas_id', $aktivitas_id)->first();

        if (!$aktivitas) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'waktu' => 'required|date',
            'keterangan' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'msgField' => $validator->errors()
            ], 422);
        }

        $aktivitas->waktu = $request->waktu;
        $aktivitas->keterangan = $request->keterangan;
        $aktivitas->save();

        return response()->json([
            'status' => true,
            'message' => 'Data log aktivitas berhasil diubah'
        ]);
    }

}

