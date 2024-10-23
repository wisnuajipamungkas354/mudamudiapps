<?php

namespace App\Http\Controllers;

use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Registrasi;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegistrasiController extends Controller
{
    public function index()
    {
        return view('registrasi', [
            'daerah' => Daerah::all(),
            'status' => Status::all()
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'daerah_id' => 'required',
            'desa_id' => 'required',
            'kelompok_id' => 'required',
            'nama' => 'required',
            // 'jk' => 'required',
            'kota_lahir' => 'required',
            'tgl_lahir' => 'required',
            // 'mubaligh' => 'required',
            'status' => 'required',
            'detail_status' => 'required',
            // 'siap_nikah' => 'required'
        ], [
            'daerah_id.required' => 'Daerah Wajib Diisi!',
            'desa_id.required' => 'Desa Wajib Diisi!',
            'kelompok_id.required' => 'Kelompok Wajib Diisi!',
            'nama.required' => 'Nama Wajib Diisi!',
            // 'jk.required' => 'Jenis Kelamin Wajib Diisi!',
            'kota_lahir.required' => 'Kota Lahir Wajib Diisi!',
            'tgl_lahir.required' => 'Tanggal Lahir Wajib Diisi!',
            // 'mubaligh.required' => 'Mubaligh Wajib Diisi!',
            'status.required' => 'Status Wajib Diisi!',
            'detail_status.required' => 'Detail Status Wajib Diisi!',
            // 'siap_nikah.required' => 'Wajib Wajib Diisi!'
        ]);

        if ($validatedData->fails()) {
            return response()->json(['errors' => 'Ada Kolom yang belum diisi, Mohon Amal Sholih dilengkapi kolom Isiannya yaa!']);
        } elseif (DB::table('mudamudis')->where('nama', '=', $request->nama)
            ->where('tgl_lahir', '=', $request->tgl_lahir)->exists()
        ) {
            return response()->json(['errors' => 'Data yang kamu masukkan sudah ada di Database!']);
        } elseif (DB::table('registrasis')->where('nama', '=', $request->nama)
            ->where('tgl_lahir', '=', $request->tgl_lahir)->exists()
        ) {
            return response()->json(['errors' => 'Data yang kamu masukkan sudah pernah di Kirim!']);
        } elseif (Carbon::parse($request->tgl_lahir)->format('Y') == Carbon::now()->format('Y')) {
            return response()->json(['errors' => 'Maaf, sepertinya tahun lahirnya belum di atur!']);
        } elseif (Carbon::parse($request->tgl_lahir)->format('Y') > Carbon::now()->format('Y')) {
            return response()->json(['errors' => 'Maaf, sepertinya tahun lahirnya belum di atur!']);
        } elseif(Carbon::parse($request->tgl_lahir)->age < 11) {
            return response()->json(['errors' => 'Maaf, Syarat umur minimal 11 tahun!']);
        } else {
            $validatedData = $request->all();
            // // Agar Nama dan Kota menjadi Camel Casing
            $validatedData['nama'] = Str::title($request['nama']);
            $validatedData['kota_lahir'] = Str::title($request['kota_lahir']);

            // // Menghitung Usia dari tgl_lahir
            $usia = Carbon::parse($request['tgl_lahir'])->age;
            $validatedData['usia'] = $usia;

            // Memasukkan semua data kedalam tabel database dan tabel update
            Registrasi::create($validatedData);
            
            return response()->json(['success' => '<p>Apabila terdapat data yang salah/ingin diubah, Segera <b>Konfirmasi</b> ke Pengurus Muda-Mudi Kelompokmu ya. <br><br><i>Mohon untuk tidak mengirim data lagi :) <br>Alhamdulillah Jazakumullohu Khoiro! </i></p>']);
        }
    }

    public function getDesa($id)
    {
        $data = Desa::query()->where('daerah_id', '=', $id)->get();
        if ($data) {
            return response()->json([
                'status' => true,
                'message' => 'Data Desa Ditemukan!',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data Desa tidak ditemukan!'
            ], 404);
        }
    }

    public function getKelompok($id)
    {
        $data = Kelompok::query()->where('desa_id', '=', $id)->get();
        if ($data) {
            return response()->json([
                'status' => true,
                'message' => 'Data Kelompok Ditemukan!',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data Kelompok tidak ditemukan!'
            ], 404);
        }
    }
}
