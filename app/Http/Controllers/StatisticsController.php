<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function showStatistics()
    {
        return view('statistics');
    }

    public function fetchPaslon()
    {
        $paslon = DB::table('calon_pemimpin')->select('id', 'nama_calon')->get();
        return response()->json($paslon);
    }

    public function fetchKategori()
    {
        $kategori = DB::table('kategori_pemilih')->select('id', 'nama')->get();
        return response()->json($kategori);
    }

    public function fetchStatistics(Request $request)
    {
        $kecamatanId = $request->input('kecamatan_id');
        $nagariId = $request->input('nagari_id');
        $paslonId = $request->input('paslon_id');
        $kategoriId = $request->input('kategori_id');

        // Initialize query
        $query = DB::table('hasil_survey as hs')
                    ->join('kecamatan as kc', 'hs.kecamatan_id', '=', 'kc.id')
                    ->join('nagari as ng', 'hs.nagari_id', '=', 'ng.id')
                    ->join('calon_pemimpin as cp', 'hs.paslon_id', '=', 'cp.id')
                    ->join('kategori_pemilih as kp', 'hs.slug', '=', 'kp.id')
                    ->select(
                        'hs.kecamatan_id',
                        'kc.name as kecamatan_name',
                        'hs.nagari_id',
                        'ng.name as nagari_name',
                        'hs.paslon_id',
                        'cp.nama_calon as paslon_name',
                        'hs.slug',
                        'kp.nama as slug_name',
                        DB::raw('COUNT(*) as count')
                    )
                    ->groupBy(
                        'hs.kecamatan_id',
                        'kc.name',
                        'hs.nagari_id',
                        'ng.name',
                        'hs.paslon_id',
                        'cp.nama_calon',
                        'hs.slug',
                        'kp.nama'
                    );

        if ($kecamatanId) {
            $query->where('hs.kecamatan_id', $kecamatanId);
        }
        if ($nagariId) {
            $query->where('hs.nagari_id', $nagariId);
        }
        if ($paslonId) {
            $query->where('hs.paslon_id', $paslonId);
        }
        if ($kategoriId) {
            $query->where('hs.slug', $kategoriId);
        }

        $data = $query->get();

        $kecamatanCount = [];
        $nagariCount = [];
        $paslonCount = [];
        $slugCount = [];

        foreach ($data as $row) {
            if (isset($kecamatanCount[$row->kecamatan_name])) {
                $kecamatanCount[$row->kecamatan_name] += $row->count;
            } else {
                $kecamatanCount[$row->kecamatan_name] = $row->count;
            }

            if (isset($nagariCount[$row->nagari_name])) {
                $nagariCount[$row->nagari_name] += $row->count;
            } else {
                $nagariCount[$row->nagari_name] = $row->count;
            }

            if (isset($paslonCount[$row->paslon_name])) {
                $paslonCount[$row->paslon_name] += $row->count;
            } else {
                $paslonCount[$row->paslon_name] = $row->count;
            }

            if (isset($slugCount[$row->slug_name])) {
                $slugCount[$row->slug_name] += $row->count;
            } else {
                $slugCount[$row->slug_name] = $row->count;
            }
        }

        return response()->json([
            'kecamatanCount' => $kecamatanCount,
            'nagariCount' => $nagariCount,
            'paslonCount' => $paslonCount,
            'slugCount' => $slugCount
        ]);
    }


    public function fetchKecamatan()
    {
        $kecamatan = DB::table('kecamatan')->select('id', 'name')->get();
        return response()->json($kecamatan);
    }

    public function fetchNagari($kecamatanId)
    {
        $nagari = DB::table('nagari')
                    ->where('kecamatan_id', $kecamatanId)
                    ->select('id', 'name')
                    ->get();
        return response()->json($nagari);
    }
    public function fetchTabel(Request $request)
{
    $kecamatanId = $request->input('kecamatan_id');
    $nagariId = $request->input('nagari_id');

    // Membangun query
    $query = DB::table('hasil_survey as hs')
        ->join('calon_pemimpin as cp', 'hs.paslon_id', '=', 'cp.id')
        ->join('kategori_pemilih as kp', 'hs.slug', '=', 'kp.id')
        ->select('hs.kecamatan_id', 'hs.nagari_id', 'hs.paslon_id', 'hs.nama', 'hs.ip_address', 'cp.nama_calon', 'kp.nama as nama_kategori')
        ->groupBy('hs.kecamatan_id', 'hs.nagari_id', 'hs.paslon_id', 'hs.nama', 'hs.ip_address', 'cp.nama_calon', 'kp.nama');

    // Menerapkan filter
    if ($kecamatanId) {
        $query->where('hs.kecamatan_id', $kecamatanId);
    }
    if ($nagariId) {
        $query->where('hs.nagari_id', $nagariId);
    }
    $query->orderBy('hs.id','DESC');
    $data = $query->get();

    // Mapping data untuk menambahkan nama kecamatan dan nagari
    $dataWithNames = $data->map(function ($item) {
        $kecamatan = DB::table('kecamatan')->where('id', $item->kecamatan_id)->first();
        $nagari = DB::table('nagari')->where('id', $item->nagari_id)->first();

        // Tambahkan nama kecamatan dan nagari, jika ditemukan
        $item->kecamatan_name = $kecamatan->name ?? 'Nama kecamatan tidak ditemukan';
        $item->nagari_name = $nagari->name ?? 'Nama nagari tidak ditemukan';

        return $item;
    });

    // Mengembalikan data sebagai JSON
    return response()->json($dataWithNames);
}

}
