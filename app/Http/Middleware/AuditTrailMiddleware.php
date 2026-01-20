<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AuditTrailMiddleware
{
     public function handle($request, Closure $next)
    {
        $response = $next($request);

        $user = Auth::user()->name ?? 'Guest';
        $action = $request->method(); // GET, POST, PUT, DELETE
        $route = $request->path(); // route name or URL
        $data = json_encode($request->all());

        AuditTrail::create([
            'user' => $user,
            'action' => $action . ' ' . $route,
            'model' => null,
            'model_id' => null,
            'changes' => $data,
        ]);

        return $response;
    }
}
