<?php

namespace App\Http\Middleware;

use Closure;

class ClinicalCenterAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->user()->userable_type !== 'App\\ClinicalCenterAdmin')
        {
            return response()->json(['status' => 'Unauthorized'],401);
        }
        return $next($request);
    }
}
