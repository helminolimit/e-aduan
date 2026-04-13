@extends('layouts.app')

@section('title', 'Edit Komentar')

@section('content')
<div class="mb-6">
    <a href="{{ route('comments.index') }}" class="text-sm text-gray-500 hover:text-blue-600">&larr; Kembali</a>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-lg">
    <h1 class="text-xl font-bold mb-6">Edit Komentar</h1>

    <form action="{{ route('comments.update', $comment) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="complaint_id" class="block text-sm font-medium text-gray-700 mb-1">Aduan <span class="text-red-500">*</span></label>
            <select id="complaint_id" name="complaint_id"
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                           {{ $errors->has('complaint_id') ? 'border-red-400' : 'border-gray-300' }}">
                <option value="">-- Pilih Aduan --</option>
                @foreach ($complaints as $complaint)
                    <option value="{{ $complaint->id }}"
                            {{ old('complaint_id', $comment->complaint_id) == $complaint->id ? 'selected' : '' }}>
                        {{ $complaint->aduan_no }} — {{ $complaint->title }}
                    </option>
                @endforeach
            </select>
            @error('complaint_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Pengirim <span class="text-red-500">*</span></label>
            <select id="user_id" name="user_id"
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                           {{ $errors->has('user_id') ? 'border-red-400' : 'border-gray-300' }}">
                <option value="">-- Pilih Pengguna --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}"
                            {{ old('user_id', $comment->user_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Isi Komentar <span class="text-red-500">*</span></label>
            <textarea id="content" name="content" rows="4"
                      class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                             {{ $errors->has('content') ? 'border-red-400' : 'border-gray-300' }}">{{ old('content', $comment->content) }}</textarea>
            @error('content')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="is_internal" name="is_internal" value="1"
                   class="rounded border-gray-300 text-blue-600"
                   {{ old('is_internal', $comment->is_internal ? '1' : '0') == '1' ? 'checked' : '' }}>
            <label for="is_internal" class="text-sm text-gray-700">Komentar Internal (tidak terlihat publik)</label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                Perbarui
            </button>
            <a href="{{ route('comments.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 px-5 py-2">Batal</a>
        </div>
    </form>
</div>
@endsection
