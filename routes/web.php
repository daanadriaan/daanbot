<?php

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

Route::middleware('session')->group(function(){
    Route::get('/', [BotController::class, 'get']);
    Route::get('/conversation', [BotController::class, 'conversation']);
    Route::post('/conversation/choose', [BotController::class, 'choose']);
});

Route::middleware('auth')->group(function(){
    Route::get('/flows', [BotController::class, 'flows']);
    Route::get('/flows/{flow}', [BotController::class, 'flow']);
    Route::post('/flows/{flow}', [BotController::class, 'storeFlow']);
    Route::post('/flows/{flow}/chats/new', [BotController::class, 'newChat']);
    Route::post('/flows/{flow}/options/new', [BotController::class, 'newOption']);

    Route::get('/maker', [BotController::class, 'maker']);
    Route::get('/maker/{id}', [BotController::class, 'maker']);
});


require __DIR__.'/auth.php';
