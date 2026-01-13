<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgencyController;

Route::prefix('agencies')->group(function () {

    Route::get('/', [AgencyController::class, 'index'])
        ->name('agencies.index');

    Route::get('/create', [AgencyController::class, 'create'])
        ->name('agencies.create');

    Route::post('/', [AgencyController::class, 'store'])
        ->name('agencies.store');

    Route::get('/{agency}/edit', [AgencyController::class, 'edit'])
        ->name('agencies.edit');

    Route::put('/{agency}', [AgencyController::class, 'update'])
        ->name('agencies.update');

    Route::delete('/{agency}', [AgencyController::class, 'destroy'])
        ->name('agencies.destroy');
});
