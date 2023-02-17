<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SocialiteCallbackRequest;
use App\Http\Requests\Auth\SocialiteRegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SocialiteController extends Controller
{
    public function register(SocialiteRegisterRequest $request): RedirectResponse
    {
        return $request->redirect();
    }

    /**
     * @throws ValidationException
     */
    public function callback(SocialiteCallbackRequest $request): View|RedirectResponse
    {
        return $request->callback();
    }
}
