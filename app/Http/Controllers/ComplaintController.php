<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::with(['complainant', 'category', 'officer'])
            ->latest()
            ->paginate(10);

        return view('complaints.index', compact('complaints'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $complainants = User::where('role', 'complainant')->orderBy('name')->get();
        $officers = User::where('role', 'officer')->orderBy('name')->get();

        return view('complaints.create', compact('categories', 'complainants', 'officers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'officer_id'  => 'nullable|exists:users,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'location'    => 'required|string|max:500',
            'status'      => 'required|in:pending,in_review,in_progress,resolved,closed,rejected',
            'priority'    => 'required|in:low,medium,high,urgent',
        ]);

        $year = date('Y');
        $prefix = 'ADU-' . $year . '-';
        $last = Complaint::where('aduan_no', 'like', $prefix . '%')->orderBy('aduan_no', 'desc')->first();
        $sequence = $last ? ((int) substr($last->aduan_no, strlen($prefix))) + 1 : 1;
        $aduanNo = $prefix . sprintf('%05d', $sequence);

        Complaint::create([
            'aduan_no'    => $aduanNo,
            'user_id'     => $request->user_id,
            'category_id' => $request->category_id,
            'officer_id'  => $request->officer_id ?: null,
            'title'       => $request->title,
            'description' => $request->description,
            'location'    => $request->location,
            'status'      => $request->status,
            'priority'    => $request->priority,
        ]);

        return redirect()->route('complaints.index')
            ->with('success', 'Aduan berjaya ditambah.');
    }

    public function edit(Complaint $complaint)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $complainants = User::where('role', 'complainant')->orderBy('name')->get();
        $officers = User::where('role', 'officer')->orderBy('name')->get();

        return view('complaints.edit', compact('complaint', 'categories', 'complainants', 'officers'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'officer_id'  => 'nullable|exists:users,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'location'    => 'required|string|max:500',
            'status'      => 'required|in:pending,in_review,in_progress,resolved,closed,rejected',
            'priority'    => 'required|in:low,medium,high,urgent',
        ]);

        $complaint->update([
            'user_id'     => $request->user_id,
            'category_id' => $request->category_id,
            'officer_id'  => $request->officer_id ?: null,
            'title'       => $request->title,
            'description' => $request->description,
            'location'    => $request->location,
            'status'      => $request->status,
            'priority'    => $request->priority,
        ]);

        return redirect()->route('complaints.index')
            ->with('success', 'Aduan berjaya dikemaskini.');
    }

    public function destroy(Complaint $complaint)
    {
        $complaint->delete();

        return redirect()->route('complaints.index')
            ->with('success', 'Aduan berjaya dipadam.');
    }
}
