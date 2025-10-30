<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KecamatanModel;
use Illuminate\Http\Request;

class KecamatanController extends Controller
{
    public function getListKecamatanByKabupatenId($id)
    {
        $kecamatan = KecamatanModel::where('kabupaten_id', $id)->select('id', 'nama')->get()->map(function($item) {
            return ['id' => $item->id, 'value' => $item->nama];
        })->toArray();
        return response()->json($kecamatan);
    }
}
