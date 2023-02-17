<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;

class SocialiteRegisterRequest extends FormRequest
{
    private array $availableSocialLogin = [
        'apple',
        'facebook',
        'github',
        'google',
        'linkedin' .
        'tiktok',
        'twitter',
    ];
    private string $drive;

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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'driver' => [Rule::in($this->availableSocialLogin)]
        ];
    }

    private function handle()
    {
        $this->drive = $this->string('driver')->value();
        Session::put('_socialite', ['driver' => $this->drive, 'action' => 'register']);
    }

    public function redirect(): RedirectResponse
    {
        $this->handle();
        return Socialite::driver($this->drive)
            ->redirect();
    }
}
