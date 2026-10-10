<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        if (empty($permissions)) {
            return $next($request);
        }

        foreach ($permissions as $permission) {
            $subPermissions = explode(',', $permission);
            foreach ($subPermissions as $sub) {
                $sub = trim($sub);
                if ($user->hasPermission($sub)) {
                    return $next($request);
                }
            }
        }

        abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses modul ini.');
    }
}
