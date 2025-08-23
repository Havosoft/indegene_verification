<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckNinVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user has failed NIN verification
            if ($user->nin_verification_status === 'failed') {
                // If it's an AJAX request, return JSON response
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Access denied',
                        'message' => $user->nin_verification_failure_reason,
                        'show_modal' => true
                    ], 403);
                }
                
                // If already on dashboard, let it proceed (to show the modal)
                if ($request->routeIs('dashboard')) {
                    // Add session data for the modal
                    session([
                        'nin_verification_failed' => true,
                        'failure_reason' => $user->nin_verification_failure_reason
                    ]);
                    return $next($request);
                }
                
                // For other routes, redirect to dashboard with modal data
                return redirect()->route('dashboard')
                    ->with('nin_verification_failed', true)
                    ->with('failure_reason', $user->nin_verification_failure_reason);
            }
        }

        return $next($request);
    }
}