<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\KasirController;
use App\Http\Controllers\Api\PsnController;
use App\Http\Controllers\Api\ModalDataController;


use App\Models\User;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Psn API routes
Route::get('/psn', [PsnController::class, 'index']);
Route::post('/psn', [PsnController::class, 'store']);
Route::get('/psn/{id}', [PsnController::class, 'show']);
Route::put('/psn/{id}', [PsnController::class, 'update']);
Route::delete('/psn/{id}', [PsnController::class, 'destroy']);

// Modal data API routes
Route::get('/modal/tindakan-tarifs', [ModalDataController::class, 'getTindakanTarifs']);
Route::get('/modal/farmalkes', [ModalDataController::class, 'getFarmalkes']);
Route::get('/test', function () {
    return response()->json(['message' => 'CORS aktif dan Kernel jalan ✅']);
});


Route::post('/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('websocket-token')->plainTextToken;

    return response()->json(['token' => $token]);
});
