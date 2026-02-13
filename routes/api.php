<?php

use App\Models\Mudamudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/import-data', function (Request $request) {
    if(request()->header('X-API-KEY') !== config('services.migration.key')) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $data = Mudamudi::with(['daerah', 'desa', 'kelompok'])->get();
    return response()->json([
        'message' => 'success',
        'data' => $data,
    ]);
});
