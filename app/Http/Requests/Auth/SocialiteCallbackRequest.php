<?php

namespace App\Http\Requests\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class SocialiteCallbackRequest extends FormRequest
{
    private \Laravel\Socialite\Contracts\User $socialData;
    private array $socialUserData;
    private array $socialite;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
            'state' => ['required', 'string']
        ];
    }

    /**
     * @throws ValidationException
     */
    private function handle()
    {
        $this->getSocialite();
        $this->getSocialData();
        $this->handleSocialData();
    }

    /**
     * @throws ValidationException
     */
    private function getSocialite()
    {
        if (!Session::has('_socialite')) throw ValidationException::withMessages([
            'driver' => __('validation.enum', ['attribute' => 'driver'])
        ]);
        $this->socialite = Session::get('_socialite');
    }

    private function getSocialData()
    {
        $this->socialData = Socialite::driver('github')->user();
    }

    private function handleSocialData()
    {
        $this->socialUserData = [
            'id' => $this->socialData->getId(),
            'nickname' => $this->socialData->getNickname() ?? null,
            'name' => $this->socialData->getName(),
            'email' => $this->socialData->getEmail(),
            'avatar' => $this->socialData->getAvatar(),
            'driver' => $this->socialite['driver']
        ];
    }

    /**
     * @throws ValidationException
     */
    public function callback(): View|RedirectResponse
    {
        $this->handle();
        if ($this->socialite['action'] === 'register') return view('auth.register', ['user' => $this->socialUserData]);
        return $this->authenticate();
    }

    private function authenticate(): RedirectResponse
    {
        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
