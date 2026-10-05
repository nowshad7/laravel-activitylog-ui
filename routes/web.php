<?php

use Illuminate\Support\Facades\Route;
use Nsd7\LaravelActivitylogUi\Http\Controllers\ActivityLogController;
use Nsd7\LaravelActivitylogUi\Http\Middleware\Authorize;

Route::group([
    'prefix' => config('activitylog-ui.route.prefix', 'admin/activity-log'),
    'domain' => config('activitylog-ui.route.domain'),
    'middleware' => array_merge((array) config('activitylog-ui.route.middleware', ['web', 'auth']), [Authorize::class]),
    'as' => 'activitylog-ui.',
], function () {
    Route::get('/', [ActivityLogController::class, 'index'])->name('index');
    Route::get('/export', [ActivityLogController::class, 'export'])->name('export');
    Route::get('/{activity}', [ActivityLogController::class, 'show'])->whereNumber('activity')->name('show');
});
