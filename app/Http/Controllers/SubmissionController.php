<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NoticeBoard;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param NoticeBoard $board
     */
    public function store(Request $request, NoticeBoard $board)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:poster,flyer',
            'content' => 'required_if:type,flyer|nullable|string',
            'file' => 'required_if:type,poster|nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('submissions', 'public');
        }
        Submission::create([
        'notice_board_id' => $board->id,
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'file_path' => $filePath,
            'status' => 'pending',
            'expires_at' => now()->addWeek(),
        ]);
        return redirect()->route('boards.show', $board)->with('success', 'Submission received!');
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
}
