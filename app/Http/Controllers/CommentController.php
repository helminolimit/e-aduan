<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with(['complaint', 'author'])
            ->latest()
            ->paginate(15);

        return view('comments.index', compact('comments'));
    }

    public function create()
    {
        $complaints = Complaint::orderBy('aduan_no')->get(['id', 'aduan_no', 'title']);
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('comments.create', compact('complaints', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'complaint_id' => 'required|exists:complaints,id',
            'user_id'      => 'required|exists:users,id',
            'content'      => 'required|string',
            'is_internal'  => 'boolean',
        ]);

        Comment::create([
            'complaint_id' => $request->complaint_id,
            'user_id'      => $request->user_id,
            'content'      => $request->content,
            'is_internal'  => $request->boolean('is_internal'),
        ]);

        return redirect()->route('comments.index')
            ->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function edit(Comment $comment)
    {
        $complaints = Complaint::orderBy('aduan_no')->get(['id', 'aduan_no', 'title']);
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('comments.edit', compact('comment', 'complaints', 'users'));
    }

    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'complaint_id' => 'required|exists:complaints,id',
            'user_id'      => 'required|exists:users,id',
            'content'      => 'required|string',
            'is_internal'  => 'boolean',
        ]);

        $comment->update([
            'complaint_id' => $request->complaint_id,
            'user_id'      => $request->user_id,
            'content'      => $request->content,
            'is_internal'  => $request->boolean('is_internal'),
        ]);

        return redirect()->route('comments.index')
            ->with('success', 'Komentar berhasil diperbarui.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->route('comments.index')
            ->with('success', 'Komentar berhasil dihapus.');
    }
}
