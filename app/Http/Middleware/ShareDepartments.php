<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\View;

class ShareDepartments
{
    public function handle(Request $request, Closure $next)
    {
        View::share('departments', Department::where('status', true)->get());

        return $next($request);
    }
}
