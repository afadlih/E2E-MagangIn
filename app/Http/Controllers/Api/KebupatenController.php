<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KabupatenModel;
use Illuminate\Http\Request;

class KebupatenController extends Controller
{
    public function getListKabupatenByProvinsiId($id)
    {
        $kabupaten = KabupatenModel::where('provinsi_id', $id)->select('id', 'nama')->get()->map(function($item) {
            return ['id' => $item->id, 'value' => $item->nama];
        })->toArray();
        return response()->json($kabupaten);
    }
}
