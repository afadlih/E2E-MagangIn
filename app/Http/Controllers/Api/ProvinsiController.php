<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProvinsiModel;
use Illuminate\Http\Request;

class ProvinsiController extends Controller
{
    public function getAllProvinsi()
    {
        $provinsi = ProvinsiModel::select('id', 'nama')->get()->map(function($item) {
            return ['id' => $item->id, 'value' => $item->nama];
        })->toArray();
        return response()->json($provinsi);
    }
}
