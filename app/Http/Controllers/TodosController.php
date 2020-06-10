<?php

namespace App\Http\Controllers;

use App\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodosController extends Controller
{
    public function index(Request $request)
    {
        $todos = Todo::where('user_id', Auth::user()->id)
            ->search($request->search)
            ->orderBy('done', 'asc')
            ->orderBy('date', 'desc')
            ->paginate(20);

        return response()->json([
            'message' => 'todos_listed',
            'todos' => $todos
        ]);
    }
}
