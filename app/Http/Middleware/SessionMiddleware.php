<?php

namespace App\Http\Middleware;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Closure;

class SessionMiddleware
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $request->conversation = Conversation::getOrCreate();

        return $next($request);
    }
}
