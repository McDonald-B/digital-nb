<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NoticeBoard;
use App\Models\BoardMembership;
use inertia\Inertia;

class NoticeBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = NoticeBoard::where('is_private', false);
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $boards = $query->with('owner')->get();
        return Inertia::render('Boards/Index', ['boards' => $boards]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_private' => 'boolean',
        ]);
        $user = Auth::user();
        $board = $user->boards()->create($validated);
        // Auto-add creator as admin member
        BoardMembership::create([
            'user_id' => Auth::id(),
            'notice_board_id' => $board->id,
            'role' => 'admin'
        ]);
        return redirect()->route('boards.show', $board);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function join(NoticeBoard $board)
    {
        if (!$board->members()->where('user_id', Auth::id())->exists()) {
            BoardMembership::create([
                'user_id' => Auth::id(),
                'notice_board_id' => $board->id,
                'role' => 'member'
            ]);
        }
        return redirect()->route('boards.show', $board);

    }
}