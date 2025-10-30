<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DesaModel;
use Illuminate\Http\Request;

class DesaController extends Controller
{
    public function getListDesaByKecamatanId($id)
    {
        $desa = DesaModel::where('kecamatan_id', $id)->select('id', 'nama')->get()->map(function ($item) {
            return ['id' => $item->id, 'value' => $item->nama];
        })->toArray();
        return response()->json($desa);
    }
}
