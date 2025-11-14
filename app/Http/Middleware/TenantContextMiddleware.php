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
        // Skip tenant context for admin panel routes that don't require a tenant
        if ($request->is('admin/*') || $request->is('api/admin/*')) {
            return $next($request);
        }

        // For app and pegawai panels, check authentication and tenant context
        if ($request->is('app/*') || $request->is('pegawai/*')) {
            // Don't run additional logic here as Filament handles auth for these panels
            return $next($request);
        }

        // For non-panel routes, apply tenant context logic
        if (!($request->is('admin/*') || $request->is('app/*') || $request->is('pegawai/*'))) {
            // Check if authenticated user exists
            if (Auth::check()) {
                $user = Auth::user();

                // Set tenant context from user's tenant_id if it exists
                if ($user->tenant_id) {
                    // You can store the tenant_id in session or request for later use
                    $request->attributes->set('tenant_id', $user->tenant_id);

                    // Optionally you could also set a global variable or use Laravel's request macro
                    // to make tenant_id easily accessible throughout the request
                }
            }

            // For API requests, you might also want to check for tenant in headers
            $tenantHeader = $request->header('X-Tenant');
            if ($tenantHeader) {
                $tenant = Tenant::where('code', $tenantHeader)->first();
                if ($tenant) {
                    $request->attributes->set('tenant_id', $tenant->id);
                }
            }
        }

        return $next($request);
    }
}
