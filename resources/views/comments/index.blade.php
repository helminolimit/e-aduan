@extends('layouts.app')

@section('title', 'Komentar')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Komentar</h1>
    <a href="{{ route('comments.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Tambah Komentar
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600 w-8">#</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Aduan</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Pengirim</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Isi Komentar</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Internal</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse ($comments as $comment)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400">{{ $comments->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3">
                        @if ($comment->complaint)
                            <span class="font-medium text-gray-800">{{ $comment->complaint->aduan_no }}</span>
                            <p class="text-xs text-gray-400 truncate max-w-[140px]">{{ $comment->complaint->title }}</p>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $comment->author?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs">
                        <p class="truncate">{{ $comment->content }}</p>
                    </td>
                    <td class="px-4 py-3">
                        @if ($comment->is_internal)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">Internal</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Publik</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-400 whitespace-nowrap">{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                        <a href="{{ route('comments.edit', $comment) }}"
                           class="text-blue-600 hover:underline text-xs">Edit</a>
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus komentar ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada komentar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($comments->hasPages())
    <div class="mt-4">{{ $comments->links() }}</div>
@endif
@endsection
