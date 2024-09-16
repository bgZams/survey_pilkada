<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WilayahData;
use DB;
use App\Models\HasilSurvey;
use RealRashid\SweetAlert\Facades\Alert;


class DataController extends Controller
{
    public function showForm()
    {
        $kecamatan = $this->fetchKecamatan();
        $kategori = DB::table('kategori_pemilih')->get();
        return view('form', compact('kecamatan','kategori'));
    }


    public function fetchKecamatan()
    {
        $response = DB::table('kecamatan')->get();
        return json_decode($response, true);
    }

    public function fetchNagari($kecamatanId)
    {
        $response = DB::table('nagari')->where('kecamatan_id',$kecamatanId)->get();
        return json_decode($response, true);
    }


    // DataController.php
    public function submitData(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required',
            'kecamatan_id' => 'required',
            'nagari_id' => 'required',
            'paslon_id' => 'required',
            'slug' => 'required',
        ]);

        // Cek apakah data sudah ada dengan ip_address yang sama dan data lainnya
        $existingData = DB::table('hasil_survey')
            ->where('ip_address', $request->ip())
            ->where('nama', $request->nama)
            ->where('kecamatan_id', $request->kecamatan_id)
            ->where('nagari_id', $request->nagari_id)
            ->where('slug', $request->slug)
            ->first();

        if ($existingData) {
            return redirect()->route('form.show')->with('error', 'Anda sudah memilih, tidak bisa submit lagi.');
        }

        // Simpan data ke database
        DB::table('hasil_survey')->insert([
            'nama' => $request->nama,
            'kecamatan_id' => $request->kecamatan_id,
            'nagari_id' => $request->nagari_id,
            'paslon_id' => $request->paslon_id,
            'slug' => $request->slug,
            'ip_address' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('form.show')->with('success', 'Data berhasil disimpan!');
    }


}

