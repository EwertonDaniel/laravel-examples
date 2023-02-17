<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class SocialiteCallbackRequest extends FormRequest
{
    private \Laravel\Socialite\Contracts\User $socialData;
    private array $socialUserData;
    private array $socialite;
    private User $user;

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
            //'code' => ['required', 'string'],
            //'state' => ['required', 'string']
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

    private function getSocialData(): void
    {
        $this->socialData = Socialite::driver($this->socialite['driver'])->user();
    }

    /**
     * @return void
     * @note Session created to persist the additional data in the database
     */
    private function handleSocialData(): void
    {
        $this->socialUserData = [
            'id' => $this->socialData->getId(),
            'nickname' => $this->socialData->getNickname() ?? null,
            'name' => $this->socialData->getName(),
            'email' => $this->socialData->getEmail(),
            'avatar' => $this->socialData->getAvatar(),
            'driver' => $this->socialite['driver']
        ];
        Session::put('_socialite_data', $this->socialUserData);
    }

    /**
     * @throws ValidationException
     */
    public function callback(): View|RedirectResponse
    {
        $this->handle();
        return $this->socialite['action'] === 'login' || $this->userExists() ?
            $this->authenticate() :
            view('auth.register', ['user' => $this->socialUserData]);
    }

    private function userExists(): bool
    {
        $this->user = User::whereEmail($this->socialUserData['email'])->first();
        return (bool)$this->user;
    }

    private function authenticate(): RedirectResponse
    {
        Auth::login($this->user);
        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
