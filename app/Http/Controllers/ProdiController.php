<?php

namespace App\Http\Controllers;

use App\Models\ProdiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;


class ProdiController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Program Studi',
            'list'  => ['Home', 'Program Studi']
        ];

        $page = (object) [
            'title' => 'Data Program Studi'
        ];

        $activeMenu = 'prodi'; // sesuaikan dengan nama menu aktif

        return view('prodi.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list()
    {
        $prodi = ProdiModel::select('prodi_id', 'nama_prodi', 'jurusan')->get();
        return DataTables::of($prodi)
            ->addIndexColumn()
            ->addColumn('aksi', function ($prodi) {
                $btn  = '<div class="btn-group" role="group">';
                $btn .= '<button onclick="modalAction(\''.url('/prodi/' . $prodi->prodi_id . '/show_ajax').'\')" class="btn btn-primary btn-sm" style="margin-right: 5px;" title="Detail Data">';
                $btn .= '<i class="fas fa-info-circle"></i></button>';
                $btn .= '<button onclick="modalAction(\''.url('/prodi/' . $prodi->prodi_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm" style="margin-right: 5px;" title="Edit Data">';
                $btn .= '<i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="modalAction(\''.url('/prodi/' . $prodi->prodi_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm" title="Hapus Data">';
                $btn .= '<i class="fas fa-trash-alt"></i></button>';
                $btn .= '</div>';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        return view('prodi.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        $validated = $request->validate([
            'nama_prodi' => 'required|string|max:100',
            'jurusan'    => 'required|string|max:100',
        ]);

        ProdiModel::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Data program studi berhasil disimpan.'
        ]);
    }

    public function show_ajax($prodi_id)
    {
        $prodi = ProdiModel::find($prodi_id);

        if (!$prodi) {
            return response()->json([
                'status' => false,
                'message' => 'Data program studi tidak ditemukan.'
            ], 404);
        }

        return view('prodi.show_ajax', compact('prodi'));
    }

    public function edit_ajax($prodi_id)
    {
        $prodi = ProdiModel::find($prodi_id);

        return view('prodi.edit_ajax', compact('prodi'));
    }

    public function update_ajax(Request $request, $prodi_id)
    {
        $prodi = ProdiModel::find($prodi_id);

        if (!$prodi) {
            return response()->json([
                'status' => false,
                'message' => 'Data program studi tidak ditemukan.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'nama_prodi' => 'required|string|max:100',
            'jurusan'    => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'msgField' => $validator->errors()
            ]);
        }

        $prodi->update($request->only('nama_prodi', 'jurusan'));

        return response()->json([
            'status' => true,
            'message' => 'Data program studi berhasil diperbarui.'
        ]);
    }

    public function delete_ajax(Request $request, $prodi_id)
    {
        $prodi = ProdiModel::find($prodi_id);

        if (!$prodi) {
            return response()->json([
                'status' => false,
                'message' => 'Data program studi tidak ditemukan.'
            ], 404);
        }

        $prodi->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data program studi berhasil dihapus.'
        ]);
    }

    public function confirm_ajax($prodi_id)
    {
        $prodi = ProdiModel::find($prodi_id);

        return view('prodi.confirm_ajax', compact('prodi'));
    }

    public function import()
    {
        return view('prodi.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // validasi file harus xls atau xlsx, max 1MB
                'file_prodi' => ['required', 'mimes:xlsx', 'max:1024']
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
            $file = $request->file('file_prodi'); // ambil file dari request
            $reader = IOFactory::createReader('Xlsx'); // load reader file excel
            $reader->setReadDataOnly(true); // hanya membaca data
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel
            $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif
            $data = $sheet->toArray(null, false, true, true); // ambil data excel
            $insert = [];
            if (count($data) > 1) { // jika data lebih dari 1 baris
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // baris ke 1 adalah header, maka lewati
                        $insert[] = [
                            'prodi_id' => $value['A'],
                            'nama_prodi' => $value['B'],
                            'jurusan' => $value['C'],
                        ];
                    }
                }

                if (count($insert) > 0) {
                    // insert data ke database, jika data sudah ada, maka diabaikan
                    ProdiModel::insertOrIgnore($insert);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport'
                ]);
            }
        }
        return redirect('/');
    }

    public function export_excel()
    {
        // ambil data prodi yang akan di export
        $prodi = ProdiModel::select('prodi_id', 'nama_prodi', 'jurusan')
            ->orderBy('prodi_id')
            ->get();

        // load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Prodi ID');
        $sheet->setCellValue('C1', 'Nama Program Studi');
        $sheet->setCellValue('D1', 'Jurusan');

        $sheet->getStyle('A1:D1')->getFont()->setBold(true); // bold header

        $no = 1;    // nomor data dimulai dari 1
        $baris = 2; // baris data dimulai dari bari ke 2
        foreach ($prodi as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->prodi_id);
            $sheet->setCellValue('C' . $baris, $value->nama_prodi);
            $sheet->setCellValue('D' . $baris, $value->jurusan);
            $baris++;
            $no++;
        }

        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true); // set auto size untuk kolom
        }

        $sheet->setTitle('Data Prodi'); // set title sheet

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx'); // create writer
        $filename = 'Data Prodi'.date('Y-m-d H:i:s').'.xlsx'; // nama file excel

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

    public function export_pdf()
    {
        $prodi = ProdiModel::select('prodi_id', 'nama_prodi', 'jurusan')
            ->orderBy('prodi_id')
            ->get();

        // use Barryvdh/DomPDF/Facade/Pdf
        $pdf = Pdf::loadView('prodi.export_pdf', ['prodi' => $prodi]);
        $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
        $pdf->render(); // generate pdf

        return $pdf->stream('Data Prodi'.date('Y-m-d H:i:s').'.pdf');
    }
}
