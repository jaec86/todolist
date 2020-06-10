<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
