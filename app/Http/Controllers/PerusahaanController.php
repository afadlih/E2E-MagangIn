<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerusahaanModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PerusahaanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Perusahaan',
            'list'  => ['Home', 'Perusahaan']
        ];

        $page = (object) [
            'title' => 'Data Perusahaan'
        ];

        $activeMenu = 'perusahaan';

        return view('perusahaan_mitra.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = PerusahaanModel::select('perusahaan_id', 'alamat', 'nama', 'email', 'telp');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function ($item) {
                    $btn  = '<div class="btn-group">';
                    $btn .= '<button onclick="modalAction(\'' . url('/perusahaan-mitra/' . $item->perusahaan_id . '/show_ajax') . '\')" class="btn btn-primary btn-sm" title="Detail"><i class="fas fa-info-circle"></i></button>';
                    $btn .= '<button onclick="modalAction(\'' . url('/perusahaan-mitra/' . $item->perusahaan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></button>';
                    $btn .= '<button onclick="modalAction(\'' . url('/perusahaan-mitra/' . $item->perusahaan_id . '/confirm_ajax') . '\')" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash-alt"></i></button>';
                    $btn .= '</div>';

                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function create_ajax()
    {
        return view('perusahaan_mitra.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama'   => 'required|max:100',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('m_perusahaan_mitra')->ignore($request->perusahaan_id, 'perusahaan_id')
                ],
                'telp' => 'required|max:20',
              'alamat' => 'required',
            ]);

            PerusahaanModel::create($validated);

            return response()->json(['status' => true, 'message' => 'Data perusahaan berhasil disimpan']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'msgField' => $e->errors(), 'message' => 'Validasi gagal']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Kesalahan: ' . $e->getMessage()]);
        }
    }

    public function show_ajax($id)
    {
        $perusahaan = PerusahaanModel::find($id);

        if (!$perusahaan) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        return view('perusahaan_mitra.show_ajax', compact('perusahaan'));
    }

    public function edit_ajax($id)
    {
        $perusahaan = PerusahaanModel::find($id);

        return view('perusahaan_mitra.edit_ajax', compact('perusahaan'));
    }

    public function update_ajax(Request $request, $id)
    {
        $perusahaan = PerusahaanModel::find($id);

        if (!$perusahaan) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        $validator = Validator::make($request->all(), [
            'nama'    => 'required|max:100',
            'email'   => 'required|email|unique:m_perusahaan_mitra,email,' . $id . ',perusahaan_id',
            'telp' => 'nullable|max:20',
            'alamat'  => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msgField' => $validator->errors(), 'message' => 'Validasi gagal']);
        }

        try {
            $perusahaan->update($request->only('nama', 'email', 'telp', 'alamat'));

            return response()->json(['status' => true, 'message' => 'Data perusahaan diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Kesalahan: ' . $e->getMessage()]);
        }
    }

    public function confirm_ajax(Request $request, $id)
    {
        $perusahaan = PerusahaanModel::find($id);
        return view('perusahaan_mitra.confirm_ajax', ['perusahaan' => $perusahaan]);
    }


    public function delete_ajax($id)
    {
        $perusahaan = PerusahaanModel::find($id);

        if (!$perusahaan) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        $perusahaan->delete();

        return response()->json(['status' => true, 'message' => 'Data perusahaan dihapus']);
    }

    public function exportPdf()
    {
        $perusahaan = PerusahaanModel::all();
        $pdf = Pdf::loadView('perusahaan_mitra.export_pdf', compact('perusahaan'));
        return $pdf->download('laporan_perusahaan.pdf');
    }

    public function export_excel()
    {
        $perusahaan = PerusahaanModel::orderBy('perusahaan_id')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Perusahaan');

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Telp');
        $sheet->setCellValue('E1', 'Alamat');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        $row = 2;
        foreach ($perusahaan as $index => $item) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $item->nama);
            $sheet->setCellValue("C{$row}", $item->email);
            $sheet->setCellValue("D{$row}", $item->telp);
            $sheet->setCellValue("E{$row}", $item->alamat);
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Perusahaan_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }


    public function import()
    {
        return view('perusahaan_mitra.import');
    }

    public function import_ajax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_perusahaan' => ['required', 'mimes:xlsx', 'max:1024']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'msgField' => $validator->errors()
            ]);
        }

        try {
            $file = $request->file('file_perusahaan');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $data = $spreadsheet->getActiveSheet()->toArray(null, false, true, true);

            $inserted = 0;
            $existingEmails = PerusahaanModel::pluck('email')->map(fn($e) => strtolower($e))->toArray();

            foreach ($data as $index => $row) {
                if ($index <= 1) continue; // Lewati baris header

                $nama    = trim($row['B']);
                $email   = strtolower(trim($row['C']));
                $telp = trim($row['D'] ?? '');
                $alamat  = trim($row['E'] ?? '');

                if (!$nama || !$email || in_array($email, $existingEmails)) continue;

                PerusahaanModel::create([
                    'nama' => $nama,
                    'email' => $email,
                    'telp' => $telp,
                    'alamat' => $alamat,
                ]);

                $inserted++;
            }

            return response()->json(['status' => true, 'message' => "$inserted data berhasil diimport"]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal import: ' . $e->getMessage()
            ]);
        }
    }
}
