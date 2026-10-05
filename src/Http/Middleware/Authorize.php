<?php

namespace Nsd7\LaravelActivitylogUi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class Authorize
{
    public function handle(Request $request, Closure $next)
    {
        $gate = config('activitylog-ui.gate');

        if ($gate && Gate::has($gate)) {
            abort_unless(Gate::forUser($request->user())->check($gate), 403);
        }

        return $next($request);
    }
}
