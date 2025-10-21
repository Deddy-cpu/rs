<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KasirController;
use App\Http\Controllers\Api\PsnController;
use App\Http\Controllers\Api\ModalDataController;

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
