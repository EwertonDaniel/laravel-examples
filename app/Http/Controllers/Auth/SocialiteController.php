<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SocialiteCallbackRequest;
use App\Http\Requests\Auth\SocialiteRegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * * @note This php document was created to be refactored as an example.
 */
class SocialiteController extends Controller
{
    /**
     * @param SocialiteRegisterRequest $request
     * @return RedirectResponse
     */
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
