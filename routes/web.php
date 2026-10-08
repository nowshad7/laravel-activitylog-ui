<?php

use Illuminate\Support\Facades\Route;
use Nsd7\LaravelActivitylogUi\Http\Controllers\ActivityLogApiController;
use Nsd7\LaravelActivitylogUi\Http\Controllers\ActivityLogController;
use Nsd7\LaravelActivitylogUi\Http\Controllers\AnalyticsController;
use Nsd7\LaravelActivitylogUi\Http\Controllers\SavedViewController;
use Nsd7\LaravelActivitylogUi\Http\Middleware\Authorize;

Route::group([
    'prefix' => config('activitylog-ui.route.prefix', 'admin/activity-log'),
    'domain' => config('activitylog-ui.route.domain'),
    'middleware' => array_merge((array) config('activitylog-ui.route.middleware', ['web', 'auth']), [Authorize::class]),
    'as' => 'activitylog-ui.',
], function () {
    Route::get('/', [ActivityLogController::class, 'index'])->name('index');
    Route::get('/export', [ActivityLogController::class, 'export'])->name('export');
    Route::get('/count', [ActivityLogController::class, 'count'])->name('count');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/data', [AnalyticsController::class, 'data'])->name('analytics.data');

    Route::post('/saved-views', [SavedViewController::class, 'store'])->name('saved-views.store');
    Route::delete('/saved-views/{savedView}', [SavedViewController::class, 'destroy'])
        ->whereNumber('savedView')->name('saved-views.destroy');

    Route::get('/api/activities', [ActivityLogApiController::class, 'index'])->name('api.index');
    Route::get('/api/activities/{activity}', [ActivityLogApiController::class, 'show'])
        ->whereNumber('activity')->name('api.show');

    // Feature-gated inside the controllers (abort 404 when disabled), kept last
    // so the numeric detail route does not shadow the named routes above.
    Route::get('/{activity}', [ActivityLogController::class, 'show'])->whereNumber('activity')->name('show');
});
