<?php

namespace App\Http\Controllers;

use App\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodosController extends Controller
{
    public function index(Request $request)
    {
        $todos = Auth::user()->todos()
            ->search($request->search)
            ->orderBy('done', 'asc')
            ->orderBy('priority', 'desc')
            ->orderBy('date', 'desc')
            ->paginate(20);

        return response()->json([
            'message' => 'todos_listed',
            'todos' => $todos
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        $validatedData = $this->validateData($request);

        $todo = new Todo($validatedData);
        $user->todos()->save($todo);

        return response()->json([
            'message' => 'todo_created',
            'todo' => $todo
        ]);
    }

    public function update(Todo $todo, Request $request)
    {
        abort_if($todo->user_id != Auth::user()->id, 404, 'todo_not_found');

        $validatedData = $this->validateData($request);

        $todo->update($validatedData);

        return response()->json([
            'message' => 'todo_updated',
            'todo' => $todo
        ]);
    }

    public function delete(Todo $todo)
    {
        abort_if($todo->user_id != Auth::user()->id, 404, 'todo_not_found');

        $todo->delete();

        return response()->json(['message' => 'todo_deleted']);
    }

    protected function validateData(Request $request)
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:255'],
            'tags' => ['array'],
            'priority' => ['required', 'numeric', 'in:1,2,3']
        ]);
    }
}
