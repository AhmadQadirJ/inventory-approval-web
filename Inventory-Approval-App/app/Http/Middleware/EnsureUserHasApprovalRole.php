<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasApprovalRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedRoles = ['General Affair', 'Finance', 'COO', 'CHRD'];

        if (!in_array($request->user()->role, $allowedRoles)) {
            // Jika role tidak diizinkan, kembalikan ke halaman dashboard
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
