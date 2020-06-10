<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate(['password' => ['required', 'string', 'min:8', 'confirmed']]);

        $user->password = Hash::make($request->password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        Auth::guard()->login($user);

        return response()->json([
            'message' => 'password_updated',
            'user' => $user
        ]);
    }
}
