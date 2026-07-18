<?php

namespace App\Http\Middleware;

use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;


class CheckActiveSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('login') || $request->is('userLogin') || $request->is('admin/login')) {
            return $next($request);
        }

        if(Auth::check()) {
            $session = UserSession::where('session_id', session()->getId())->where('user_id', Auth::id())->first();
            if(!$session || !$session->is_active){
                $reason = Cache::pull('logout_reason_'.session()->getId());
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                // simpan lagi setelah session baru dibuat
                return redirect()
                    ->route('login')
                    ->with(
                        'forced_logout',
                        $reason === 'admin' ? 'admin' : 'other_device'
                    );
            }

            $session->update([
                'last_activity'=>now()
            ]);
        }

        return $next($request);
    }
}