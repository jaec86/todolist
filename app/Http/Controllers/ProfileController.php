<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function user()
    {
        return response()->json([
            'message' => 'user_found',
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
        ]);

        $user->update($validatedData);

        return response()->json([
            'message' => 'profile_updated',
            'user' => $user
        ]);
    }
}
