<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    private const ALLOWED_PROVIDERS = ['github', 'google'];

    /**
     * Redirect the user to the provider's OAuth page for login.
     */
    public function redirectToLogin(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        session(['social_action' => 'login']);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Redirect the authenticated user to the provider's OAuth page to connect their account.
     */
    public function redirectToConnect(Request $request, string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        session([
            'social_action'  => 'connect',
            'social_user_id' => $request->user()->id,
        ]);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the OAuth callback for both login and connect flows.
     */
    public function handleCallback(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        $socialUser = Socialite::driver($provider)->user();
        $action     = session()->pull('social_action', 'login');

        if ($action === 'connect') {
            return $this->handleConnect($socialUser, $provider);
        }

        return $this->handleLogin($socialUser, $provider);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function handleLogin(mixed $socialUser, string $provider): RedirectResponse
    {
        // Find existing social account
        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            $socialAccount->update([
                'token'         => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
                'avatar'        => $socialUser->getAvatar(),
            ]);
            $user = $socialAccount->user;
        } else {
            // Match by email to avoid duplicates
            $user = User::where('email', $socialUser->getEmail())->first();

            if (! $user) {
                $user = User::create([
                    'name'     => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email'    => $socialUser->getEmail(),
                    'password' => bcrypt(str()->random(32)),
                ]);
            }

            $user->socialAccounts()->create([
                'provider'      => $provider,
                'provider_id'   => $socialUser->getId(),
                'email'         => $socialUser->getEmail(),
                'nickname'      => $socialUser->getNickname(),
                'avatar'        => $socialUser->getAvatar(),
                'token'         => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }

    private function handleConnect(mixed $socialUser, string $provider): RedirectResponse
    {
        $userId = session()->pull('social_user_id');

        $user = User::findOrFail($userId);

        $user->socialAccounts()->updateOrCreate(
            ['provider' => $provider, 'provider_id' => $socialUser->getId()],
            [
                'email'         => $socialUser->getEmail(),
                'nickname'      => $socialUser->getNickname(),
                'avatar'        => $socialUser->getAvatar(),
                'token'         => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
            ]
        );

        return redirect()->route('profile.edit')->with('status', 'social-connected');
    }

    /**
     * Disconnect a social account from the authenticated user's profile.
     */
    public function disconnect(Request $request, string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        $request->user()->socialAccounts()->where('provider', $provider)->delete();

        return redirect()->route('profile.edit')->with('status', 'social-disconnected');
    }

    private function validateProvider(string $provider): void
    {
        if (! in_array($provider, self::ALLOWED_PROVIDERS, strict: true)) {
            abort(404);
        }
    }
}
