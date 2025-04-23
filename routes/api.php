<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SimulacaoController;

Route::get('/instituicoes', [SimulacaoController::class, 'getInstituicoes']);
Route::get('/convenios', [SimulacaoController::class, 'getConvenios']);
Route::post('/simulacao', [SimulacaoController::class, 'simularCredito']);