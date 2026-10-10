<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureServiceRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $service = $request->user()?->service_sigle;
        $allowed = match ($role) {
            'SNR' => $service === 'SNR',
            'other' => $service !== null && $service !== 'SNR',
            default => false,
        };

        if (! $allowed) {
            if ($service === null) {
                return redirect()->route('login');
            }

            if (! $request->expectsJson()) {
                return redirect()->to($service === 'SNR' ? '/snr' : '/other_service');
            }

            abort(403);
        }

        return $next($request);
    }
}
