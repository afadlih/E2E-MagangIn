<?php

namespace App\Http\Controllers;

use App\Models\LamaranMagangModel;
use App\Models\NotifikasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MessageController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Message',
            'list'  => ['Home', 'Message']
        ];

        $page = (object) [
            'title' => 'Data Message'
        ];

        $activeMenu = 'message'; // set menu yang sedang aktif


        return view('message.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list()
    {
        $mhs_nim = Auth::user()->mahasiswa->mhs_nim;
        $message = NotifikasiModel::select('notifikasi_id', 'judul', 'pesan', 'waktu_dibuat', 'status_baca')
            ->where('mhs_nim', $mhs_nim)
            ->get();

        return DataTables::of($message)
            ->addIndexColumn()
            ->addColumn('aksi', function ($msg) {
                $btn  = '<div class="btn-group" role="group">';
                $btn .= '<button onclick="modalAction(\''.url('/message/' . $msg->notifikasi_id . '/show_ajax').'\')" class="btn btn-primary btn-sm" style="margin-right: 5px;" title="Detail Data">';
                $btn .= '<i class="fas fa-info-circle"></i></button>';
                $btn .= '<button onclick="markAsRead('.$msg->notifikasi_id.')" class="btn btn-success btn-sm" title="Tandai Sudah Dibaca">';
                $btn .= '<i class="fas fa-check"></i></button>';
                $btn .= '</div>';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function show_ajax($notifikasi_id)
    {

        $notifikasi = NotifikasiModel::find($notifikasi_id);

        if (!$notifikasi) {
            return response()->json([
                'status' => false,
                'message' => 'Data notifikasi dengan id ' . $notifikasi_id . ' tidak ditemukan.'
            ], 404);
        }

        return view('message.show_ajax', [
            'notifikasi' => $notifikasi
        ]);
    }

    public function markAsRead($notifikasi_id)
    {
        $userNim = Auth::user()->mahasiswa->mhs_nim;

        $notifikasi = NotifikasiModel::where('notifikasi_id', $notifikasi_id)
            ->where('mhs_nim', $userNim)
            ->first();

        if (!$notifikasi) {
            return response()->json([
                'status' => false,
                'message' => 'Data notifikasi tidak ditemukan atau bukan milik Anda.'
            ], 404);
        }

        if ($notifikasi->status_baca == 1) {
            return response()->json([
                'status' => true,
                'message' => 'Notifikasi sudah ditandai sebagai dibaca.'
            ]);
        }

        $notifikasi->update(['status_baca' => 1]);

        return response()->json([
            'status' => true,
            'message' => 'Notifikasi berhasil ditandai sudah dibaca.'
        ]);
    }
}
