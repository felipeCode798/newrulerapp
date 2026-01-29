<?php

use App\Http\Controllers\LandingPageController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('landing-page');
// })->name('landing.page');

Route::get('/', [LandingPageController::class, 'index'])->name('landing.page');

Route::get('/factura/download/{filename}', function ($filename) {
    $path = storage_path('app/temp/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path, [
        'Content-Type' => 'text/html',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
})->name('factura.download');

Route::get('/factura/view/{id}', [\App\Http\Controllers\FacturaController::class, 'verPDF'])
    ->name('factura.view');

Route::get('/buscar-procesos', [LandingPageController::class, 'buscarProcesos'])->name('buscar.procesos');