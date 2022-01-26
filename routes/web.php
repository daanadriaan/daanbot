<?php

use App\Http\Controllers\MakerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BotController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['session', 'throttle:50'])->group(function(){
    Route::get('/', [BotController::class, 'get']);
    Route::get('/conversation', [BotController::class, 'conversation']);
    Route::post('/conversation/choose', [BotController::class, 'choose']);
    Route::post('/conversation/interpret', [BotController::class, 'interpret']);
});

Route::middleware('auth')->group(function(){
    Route::get('/flows', [MakerController::class, 'flows']);
    Route::post('/flows/create', [MakerController::class, 'newFlow']);
    Route::get('/flows/{flow}', [MakerController::class, 'flow']);
    Route::post('/flows/{flow}', [MakerController::class, 'storeFlow']);
    Route::post('/flows/{flow}/update', [MakerController::class, 'updateFlow']);
    Route::post('/flows/{flow}/steps/new', [MakerController::class, 'newChat']);
    Route::get('/maker', [MakerController::class, 'maker']);
    Route::get('/maker/{id}', [MakerController::class, 'maker']);
});


require __DIR__.'/auth.php';
