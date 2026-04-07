<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        $pendingSubmissions = Submission::with(['board', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'title' => $submission->title,
                    'type' => $submission->type,
                    'content' => $submission->content,
                    'file_path' => $submission->file_path,
                    'status' => $submission->status,
                    'board' => $submission->board
                        ? [
                            'id' => $submission->board->id,
                            'name' => $submission->board->name,
                        ]
                        : null,
                    'user' => $submission->user
                        ? [
                            'id' => $submission->user->id,
                            'name' => $submission->user->name,
                        ]
                        : null,
                ];
            });

        return Inertia::render('Admin/Index', [
            'pendingSubmissions' => $pendingSubmissions,
        ]);
    }

    public function approve(Submission $submission)
    {
        $submission->update([
            'status' => 'approved',
        ]);

        return redirect()
            ->route('admin.index')
            ->with('success', 'Submission approved!');
    }

    public function reject(Submission $submission)
    {
        $submission->update([
            'status' => 'rejected',
        ]);

        return redirect()
            ->route('admin.index')
            ->with('success', 'Submission rejected!');
    }

    public function destroy(Submission $submission)
    {
        if ($submission->file_path) {
            Storage::disk('public')->delete($submission->file_path);
        }

        $submission->delete();

        return redirect()
            ->route('admin.index')
            ->with('success', 'Submission deleted!');
    }
}
