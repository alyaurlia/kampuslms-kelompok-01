<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            abort(401, 'Anda harus login terlebih dahulu.');
        }

        abort_unless(in_array($request->user()->role, $roles), 403, 'Anda tidak berhak mengakses halaman ini.');

        return $next($request);
    }
}