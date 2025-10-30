<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DesaModel;
use App\Models\KabupatenModel;
use App\Models\KecamatanModel;
use App\Models\ProvinsiModel;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function searchLocations(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $query = $request->input('query');

        $provinsi = ProvinsiModel::where('nama', 'like', '%' . $query . '%')
            ->limit(3)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'value' => $item->nama,
                    'type' => 'provinsi'
                ];
            })
            ->toArray();

        $kabupaten = KabupatenModel::with('provinsi')
            ->where('nama', 'like', '%' . $query . '%')
            ->limit(3)
            ->get()
            ->map(function($item) {
                $provinsiNama = isset($item->provinsi) ? $item->provinsi->nama : '';
                return [
                    'id' => $item->id,
                    'value' => "{$item->nama}" . ($provinsiNama ? ", {$provinsiNama}" : ""),
                    'type' => 'kabupaten'
                ];
            })
            ->toArray();

        $kecamatan = KecamatanModel::with('kabupaten', 'kabupaten.provinsi')
            ->where('nama', 'like', '%' . $query . '%')
            ->limit(3)
            ->get()
            ->map(function($item) {
                $kabupatenNama = isset($item->kabupaten) ? $item->kabupaten->nama : '';
                $provinsiNama = isset($item->kabupaten) && isset($item->kabupaten->provinsi) ? $item->kabupaten->provinsi->nama : '';

                $value = $item->nama;
                if ($kabupatenNama) $value .= ", {$kabupatenNama}";
                if ($provinsiNama) $value .= ", {$provinsiNama}";

                return [
                    'id' => $item->id,
                    'value' => $value,
                    'type' => 'kecamatan'
                ];
            })
            ->toArray();

        $desa = DesaModel::with('kecamatan', 'kecamatan.kabupaten', 'kecamatan.kabupaten.provinsi')
            ->where('nama', 'like', '%' . $query . '%')
            ->limit(3)
            ->get()
            ->map(function($item) {
                $kecamatanNama = isset($item->kecamatan) ? $item->kecamatan->nama : '';
                $kabupatenNama = isset($item->kecamatan) && isset($item->kecamatan->kabupaten) ? $item->kecamatan->kabupaten->nama : '';
                $provinsiNama = isset($item->kecamatan) && isset($item->kecamatan->kabupaten) && isset($item->kecamatan->kabupaten->provinsi) ? $item->kecamatan->kabupaten->provinsi->nama : '';

                $value = $item->nama;
                if ($kecamatanNama) $value .= ", {$kecamatanNama}";
                if ($kabupatenNama) $value .= ", {$kabupatenNama}";
                if ($provinsiNama) $value .= ", {$provinsiNama}";

                return [
                    'id' => $item->id,
                    'value' => $value,
                    'type' => 'desa'
                ];
            })
            ->toArray();

        $allResults = array_merge($provinsi, $kabupaten, $kecamatan, $desa);

        return response()->json([
            'status' => 'success',
            'data' => $allResults,
            'count' => count($allResults)
        ]);
    }
}
