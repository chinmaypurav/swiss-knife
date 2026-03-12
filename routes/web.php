<?php

use App\Http\Controllers\S3\FileManagerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::prefix('files/s3')->name('s3.')->group(function () {
    Route::get('/', [FileManagerController::class, 'index'])->name('index');
    Route::post('/upload', [FileManagerController::class, 'upload'])->name('upload');
    Route::get('/download', [FileManagerController::class, 'download'])->name('download');
    Route::post('/signed-url', [FileManagerController::class, 'signedUrl'])->name('signed-url');
    Route::delete('/{path}', [FileManagerController::class, 'destroy'])->name('destroy')->where('path', '.*');
});
