<?php

// WilayahController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function submitWilayah(Request $request)
    {
        // Validasi input
        $request->validate([
            'kecamatan_id' => 'required',
            'nagari_id' => 'required',
            'slug' => 'required',
            'nama' => 'required',
        ]);
        return redirect()->route('photo.selection')->with([
            'kecamatan_id' => $request->kecamatan_id,
            'nagari_id' => $request->nagari_id,
            'nama' => $request->nama,
            'slug' => $request->slug
        ]);
    }
}
