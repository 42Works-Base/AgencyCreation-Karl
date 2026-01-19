<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\WorkerImportController;


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

Route::prefix('workers')->name('workers.')->group(function () {

    // Show import form
    Route::get('/import', [WorkerImportController::class, 'create'])
        ->name('import.form');

    // Handle CSV upload
    Route::post('/import', [WorkerImportController::class, 'store'])
        ->name('import');

    // Download sample CSV
    Route::get('/sample-csv', [WorkerImportController::class, 'downloadSample'])
        ->name('sample.csv');

    // Worker inbox
    Route::get('/', [WorkerImportController::class, 'index'])
        ->name('index');

    // Edit worker
    Route::get('/{worker}/edit', [WorkerImportController::class, 'edit'])
        ->name('edit');

    // Update worker
    Route::put('/{worker}', [WorkerImportController::class, 'update'])
        ->name('update');

    // Delete worker
    Route::delete('/{worker}', [WorkerImportController::class, 'destroy'])
        ->name('destroy');
});
