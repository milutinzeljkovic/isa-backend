<?php

namespace App\Http\Middleware;

use Closure;

class Jsonify
{

    /**
    * Handle an incoming request 1.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $mikikikiriki = 'mikikii';
        $request->headers->set('Accept', 'application/json');
        return $next($request);
    }
}
