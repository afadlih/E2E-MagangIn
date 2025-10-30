<?php

namespace App\Http\Controllers;

use App\Models\PeriodeMagangModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
class PeriodeController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Periode',
            'list'  => ['Home', 'Periode']
        ];

        $page = (object) [
            'title' => 'Data Periode'
        ];

        $activeMenu = 'periode'; // set menu yang sedang aktif

        return view('periode.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list()
    {
        $periode = PeriodeMagangModel::select('periode_id', 'periode', 'keterangan')->get();
        return DataTables::of($periode)
            ->addIndexColumn()
            ->addColumn('aksi', function ($prd) {
                $btn  = '<div class="btn-group" role="group">';
                $btn .= '<button onclick="modalAction(\''.url('/periode/' . $prd->periode_id . '/show_ajax').'\')" class="btn btn-primary btn-sm" style="margin-right: 5px;" title="Detail Data">';
                $btn .= '<i class="fas fa-info-circle"></i></button>';
                $btn .= '<button onclick="modalAction(\''.url('/periode/' . $prd->periode_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm" style="margin-right: 5px;" title="Edit Data">';
                $btn .= '<i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="modalAction(\''.url('/periode/' . $prd->periode_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm" title="Hapus Data">';
                $btn .= '<i class="fas fa-trash-alt"></i></button>';
                $btn .= '</div>';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        return view('periode.create_ajax');
    }



    public function store_ajax(Request $request)
    {
        $validatedPeriode = $request->validate([
            'periode' => 'required',
            'keterangan' => 'required',
        ]);

        PeriodeMagangModel::create([
            'periode' => $validatedPeriode['periode'],
            'keterangan' => $validatedPeriode['keterangan'],
        ]);

       return response()->json([
        'status' => true,
        'message' => 'Data periode berhasil disimpan'
        ]);
    }

    public function confirm_ajax($periode_id)
    {
        $periode = PeriodeMagangModel::select()->where('periode_id', $periode_id)->first();

        return view('periode.confirm_ajax', compact('periode'));
    }

    public function delete_ajax(Request $request, $periode_id)
    {
        try {
            $periode = PeriodeMagangModel::where('periode_id', $periode_id)->first();

            if (!$periode) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data periode tidak ditemukan.'
                ], 404);
            }

            // Hapus periode
            $periode->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data periode berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show_ajax(String $periode_id)
    {
        $periode = PeriodeMagangModel::select('periode_id', 'periode', 'keterangan')->where('periode_id', $periode_id)->first();

        if (!$periode) {
            return response()->json([
                'status' => false,
                'message' => 'Data periode dengan id ' . $periode_id . ' tidak ditemukan.'
            ], 404);
        }

        return view('periode.show_ajax', [
            'periode' => $periode
        ]);
    }

    public function edit_ajax($periode_id)
    {
        $periode = PeriodeMagangModel::select()->where('periode_id', $periode_id)->first();

        return view('periode.edit_ajax', compact('periode'));
    }

    public function update_ajax(Request $request, $periode_id)
    {
        $periode = PeriodeMagangModel::select()->find($periode_id);

        if (!$periode) {
            return response()->json([
                'status' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ]);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'periode'  => 'required|max:50',
            'keterangan' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal, periksa input anda.',
                'msgField' => $validator->errors()
            ]);
        }

        try {
            // Update data periode
            $periode->periode = $request->periode;
            $periode->keterangan = $request->keterangan;
            $periode->save();

            return response()->json([
                'status' => true,
                'message' => 'Data periode berhasil diperbarui.'
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
        $periode = DB::table('m_periode_magang')
            ->orderBy('periode_id', 'asc')
            ->get();

        $pdf = Pdf::loadView('periode.export_pdf', ['periode' => $periode]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data Periode Magang ' . date('Y-m-d H:i:s') . '.pdf');
    }


    public function export_excel()
    {
        $periode = DB::table('m_periode_magang')
            ->orderBy('periode_id', 'asc')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Periode');
        $sheet->setCellValue('C1', 'Keterangan');
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;

        foreach ($periode as $data) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $data->periode);
            $sheet->setCellValue('C' . $baris, $data->keterangan);
            $no++;
            $baris++;
        }

        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Periode Magang');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Periode_Magang_' . date('Y-m-d_H-i-s') . '.xlsx';

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
        return view('periode.import'); // Pastikan view-nya benar
    }

    public function import_ajax(Request $request)
    {
        try {
            $rules = [
                'file_periode' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_periode');

            if (!$file->isValid()) {
                return response()->json(['status' => false, 'message' => 'File tidak valid'], 400);
            }

            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = storage_path('app/public/file_periode');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0775, true);
            }

            $file->move($destinationPath, $filename);
            $filePath = storage_path("app/public/file_periode/$filename");

            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            // hapus file setelah digunakan
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $existingPeriode = DB::table('m_periode_magang')->pluck('periode')->toArray();
            $insertedCount = 0;

            foreach ($data as $index => $row) {
                if ($index <= 1) continue; // Lewati header

                $periode = trim($row['B'] ?? '');
                $keterangan = trim($row['C'] ?? '');

                if (!$periode) continue;

                if (in_array($periode, $existingPeriode) && in_array($keterangan, $existingPeriode)) {
                    continue;
                }

                DB::table('m_periode_magang')->insert([
                    'periode' => $periode,
                    'keterangan' => $keterangan,
                ]);

                $insertedCount++;
            }

            if ($insertedCount > 0) {
                return response()->json([
                    'status' => true,
                    'message' => "$insertedCount periode berhasil diimpor"
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data periode baru yang diimpor'
                ]);
            }

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat import data'
            ]);
        }
    }


}