<?php

namespace App\Http\Middleware;

use App\Support\RoleRouter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            // Send them to their own home rather than a hard 403.
            if ($user) {
                return redirect()->route(RoleRouter::homeRouteFor($user));
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}
