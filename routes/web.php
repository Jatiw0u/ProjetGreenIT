<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('intensity');
});

Route::get('/intensity', [LocationController::class, 'create'])->name('intensity');

Route::get('/energy-demand', [LocationController::class, 'create'])->name('energy-demand');

Route::post('/save-alert-settings', function (Request $request) {
    // Sauvegarder les paramètres d'alerte ici
    $threshold = $request->input('carbonThreshold');
    $notificationMethod = $request->input('notificationMethod');
    // Enregistrer ces valeurs dans la base de données ou prendre les actions appropriées
    return response()->json(['success' => true]);
});

// Requêtes API
Route::get('/api/List_Location', [ApiController::class, 'List_City']);
Route::get('/api/carbone-intensities/{id}', [ApiController::class, 'List_CarboneIntensitiesLocation']);
Route::get('/api/electrical-demands/{id}', [ApiController::class, 'List_ElectricalDemandLocation']);

