<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
{
    /**
     * Convert the response contract into an HTTP response.
     */
    public function toResponse($request): Response
    {
        $user = $request->user();

        if ($user && strtolower(trim($user->role ?? '')) === 'admin') {
            return $request->wantsJson()
                ? new JsonResponse(['two_factor' => false], 200)
                : redirect()->intended(route('admin.dashboard'));
        }

        $team = $user?->currentTeam ?? $user?->personalTeam();

        if (! $team) {
            abort(403);
        }

        URL::defaults(['current_team' => $team->slug]);

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 200)
            : redirect()->intended("/{$team->slug}".Fortify::redirects('login'));
    }
}
