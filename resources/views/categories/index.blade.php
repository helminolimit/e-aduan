@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Kategori</h1>
    <a href="{{ route('categories.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Tambah Kategori
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600 w-8">#</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Nama</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Deskripsi</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Dibuat</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse ($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400">{{ $categories->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $category->description ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if ($category->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-600">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-400">{{ $category->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                        <a href="{{ route('categories.edit', $category) }}"
                           class="text-blue-600 hover:underline text-xs">Edit</a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada kategori.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($categories->hasPages())
    <div class="mt-4">{{ $categories->links() }}</div>
@endif
@endsection
