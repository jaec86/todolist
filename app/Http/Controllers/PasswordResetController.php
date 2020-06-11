<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    use ResetsPasswords;

    protected function sendResetResponse(Request $request, $response)
    {
        return response()->json([
            'message' => trans($response),
            'user' => Auth::user()
        ], 200);
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        throw ValidationException::withMessages(['email' => [trans($response)]]);
    }
}
