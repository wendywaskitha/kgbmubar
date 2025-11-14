<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class TenantContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip tenant context for all Filament panel routes
        // This prevents interference with Filament's authentication system
        if ($request->is('admin') || 
            $request->is('admin/*') || 
            $request->is('app') ||
            $request->is('app/*') || 
            $request->is('pegawai') ||
            $request->is('pegawai/*')) {
            return $next($request);
        }

        // For non-panel routes, apply tenant context logic
        // Check if authenticated user exists
        if (Auth::check()) {
            $user = Auth::user();

            // Set tenant context from user's tenant_id if it exists
            if ($user->tenant_id) {
                // Store the tenant_id in request attributes for later use
                $request->attributes->set('tenant_id', $user->tenant_id);
            }
        }

        // For API requests, check for tenant in headers
        $tenantHeader = $request->header('X-Tenant');
        if ($tenantHeader) {
            $tenant = Tenant::where('code', $tenantHeader)->first();
            if ($tenant) {
                $request->attributes->set('tenant_id', $tenant->id);
            }
        }

        return $next($request);
    }
}
