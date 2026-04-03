<?php

use Illuminate\Support\Facades\Route;
use Storage\ApiReplay\Http\Controllers\ApiReplayController;

Route::prefix(config('api-replay.path', 'api-replay'))->middleware(config('api-replay.middleware', ['web']))->group(function () {
    Route::get('/', [ApiReplayController::class, 'index'])->name('api-replay.index');
    Route::get('/{uuid}', [ApiReplayController::class, 'show'])->name('api-replay.show');
    Route::post('/{uuid}/replay', [ApiReplayController::class, 'replay'])->name('api-replay.replay');
});
