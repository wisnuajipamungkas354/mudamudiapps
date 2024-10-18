<?php

namespace App\Http\Controllers;

use App\Models\Daerah;
use App\Models\Dapukan;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\RegistrasiPengurus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegistrasiPengurusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('registrasi-pengurus', [
            'dapukan' => Dapukan::all()
        ]);
    }

    public function getOptions($tingkatan)
    {
        if($tingkatan == 'Daerah') {
            $data['nama_tingkatan'] = Daerah::query()->pluck('nm_daerah');
            $data['dapukan'] = Dapukan::query()->where('tingkatan', '=', $tingkatan)->pluck('nama_dapukan');
        } elseif($tingkatan == 'Desa') {
            $data['nama_tingkatan'] = Desa::query()->pluck('nm_desa');
            $data['dapukan'] = Dapukan::query()->where('tingkatan', '=', $tingkatan)->pluck('nama_dapukan');
        } elseif($tingkatan == 'Kelompok') {
            $data['nama_tingkatan'] = Kelompok::query()->pluck('nm_kelompok');
            $data['dapukan'] = Dapukan::query()->where('tingkatan', '=', $tingkatan)->pluck('nama_dapukan');
        }

        if ($data) {
            return response()->json([
                'status' => true,
                'message' => 'Data Options Ditemukan!',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data Options tidak ditemukan!'
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
         'tingkatan' => 'required',
         'nama_tingkatan' => 'required',
         'dapukan' => 'required',
         'nama_pengurus' => 'required|min:3',
         'no_hp' => 'required|min:11'
        ], [
            'tingkatan.required' => 'Tingkatan Wajib Diisi!',
            'nama_tingkatan.required' => 'Nama Tingkatan Wajib Diisi!',
            'nama_dapukan.required' => 'Dapukan Wajid Diisi!',
            'nama_pengurus.required' => 'Nama Lengkap Harus Diisi!',
            'nama_pengurus.min' => 'Nama Minimal 3 huruf',
            'no_hp.required' => 'Nomor HP Wajib Diisi!',
            'no_hp.min' => 'Nomor HP Minimal 11 Huruf!',
        ]);

        if($validatedData->fails()) {
            return response()->json(['errors' => $validatedData->errors()->first()]);
        } elseif(DB::table('pengurus_sedaerahs')
        ->where('nama_pengurus', $request->nama_pengurus)
        ->where('tingkatan', $request->tingkatan)
        ->where('nama_tingkatan', $request->nama_tingkatan)
        ->where('dapukan', $request->dapukan)
        ->exists()) {
            return response()->json(['errors' => 'Data yang kamu masukkan sudah ada di Database!']);
        } elseif(DB::table('registrasi_penguruses')
        ->where('nama_pengurus', $request->nama_pengurus)
        ->where('tingkatan', $request->tingkatan)
        ->where('nama_tingkatan', $request->nama_tingkatan)
        ->where('dapukan', $request->dapukan)
        ->exists()) {
            return response()->json(['errors' => 'Data yang kamu masukkan sudah pernah di Kirim!']);
        } else {
            $validatedData = $request->all();
            $validatedData['tingkatan'] = Str::title($request['tingkatan']);
            $validatedData['dapukan'] = Str::title($request['dapukan']);
            $validatedData['nama_pengurus'] = Str::title($request['nama_pengurus']);

            RegistrasiPengurus::create($validatedData);
            return response()->json(['success' => 'Datamu berhasil dikirim, Alhamdulillah Jazakumullohu Khoiro!']);
        }
    }
}
