<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ... $roles)
    {
        foreach ($roles as $role) {
            if (session('roleCode') == $role) {
                return $next($request);
            }
        }

        return response()->json([ 'error'=> 403, 'msg' => 'Acceso Restringido' ], 403);
        //abort(403);
    }
}
