<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class StatisticsController extends Controller
{
    // Menampilkan halaman statistik
    public function showStatistics()
    {
        // $query = DB::table('hasil_survey')
        //             ->join('calon_pemimpin as cp','hasil_survey.gbr_id','=','cp.id')
        //             ->join('kategori_pemilih as kp','hasil_survey.cat','=','kp.id')
        //             ->select('hasil_survey.kec', 'hasil_survey.kel', 'hasil_survey.gbr_id','hasil_survey.nama', 'hasil_survey.ip_address','cp.nama_calon','kp.nama as nama_kategori')
        //             ->groupBy('hasil_survey.kec', 'hasil_survey.kel', 'hasil_survey.gbr_id','hasil_survey.nama', 'hasil_survey.ip_address','cp.nama_calon','kp.nama');

        // // // Filter kecamatan
        // // if ($request->query('kecamatan')) {
        // //     $query->where('hasil_survey.kec', $request->query('kecamatan'));
        // // }

        // // // Filter kelurahan
        // // if ($request->query('kelurahan')) {
        // //     $query->where('hasil_survey.kel', $request->query('kelurahan'));
        // // }

        // $data = $query->get();

        // // Mapping data, ambil nama kecamatan dan kelurahan
        // $dataWithNames = $data->map(function($item) {
        //     $item->kecamatan_id = $item->kec;
        //     $item->kelurahan_id = $item->kel;

        //     // Mengambil nama kecamatan dan kelurahan dari API
        //     $kecamatanResponse = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/district/{$item->kec}.json");
        //     $kelurahanResponse = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/village/{$item->kel}.json");

        //     // Cek respons API, ambil nama jika valid
        //     $item->kecamatan_name = $kecamatanResponse->json()['name'] ?? 'Nama kecamatan tidak ditemukan';
        //     $item->kelurahan_name = $kelurahanResponse->json()['name'] ?? 'Nama kelurahan tidak ditemukan';

        //     return $item;
        // });

        // // Mengembalikan data sebagai JSON
        // dd(json_encode($dataWithNames));

        return view('statistics');
    }

    // Fetch Statistics dengan filter
    public function fetchStatistics(Request $request)
    {
        $query = DB::table('hasil_survey')
                    ->join('calon_pemimpin as cp','hasil_survey.gbr_id','=','cp.id')
                    ->join('kategori_pemilih as kp','hasil_survey.cat','=','kp.id')
                    ->select('hasil_survey.kec', 'hasil_survey.kel', 'hasil_survey.gbr_id','hasil_survey.nama', 'hasil_survey.ip_address','cp.nama_calon','kp.nama as nama_kategori')
                    ->groupBy('hasil_survey.kec', 'hasil_survey.kel', 'hasil_survey.gbr_id','hasil_survey.nama', 'hasil_survey.ip_address','cp.nama_calon','kp.nama');

        // Filter kecamatan
        if ($request->query('kecamatan')) {
            $query->where('hasil_survey.kec', $request->query('kecamatan'));
        }

        // Filter kelurahan
        if ($request->query('kelurahan')) {
            $query->where('hasil_survey.kel', $request->query('kelurahan'));
        }

        $data = $query->get();

        // Mapping data, ambil nama kecamatan dan kelurahan
        $dataWithNames = $data->map(function($item) {
            $item->kecamatan_id = $item->kec;
            $item->kelurahan_id = $item->kel;

            // Mengambil nama kecamatan dan kelurahan dari API
            $kecamatanResponse = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/district/{$item->kec}.json");
            $kelurahanResponse = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/village/{$item->kel}.json");

            // Cek respons API, ambil nama jika valid
            $item->kecamatan_name = $kecamatanResponse->json()['name'] ?? 'Nama kecamatan tidak ditemukan';
            $item->kelurahan_name = $kelurahanResponse->json()['name'] ?? 'Nama kelurahan tidak ditemukan';

            return $item;
        });

        // Mengembalikan data sebagai JSON
        return response()->json($dataWithNames);
    }


    // Fetch Kecamatan berdasarkan kabupaten ID
    public function fetchKecamatan($kabupatenId)
    {
        $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/districts/{$kabupatenId}.json");
        return response()->json($response->json());
    }

    // Fetch Kelurahan berdasarkan kecamatan ID
    public function fetchKelurahan($kecamatanId)
    {
        $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/villages/{$kecamatanId}.json");
        return response()->json($response->json());
    }
}
