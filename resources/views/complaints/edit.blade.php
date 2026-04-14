@extends('layouts.app')

@section('title', 'Edit Aduan')

@section('content')
<div class="mb-6">
    <a href="{{ route('complaints.index') }}" class="text-sm text-gray-500 hover:text-blue-600">&larr; Kembali</a>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold">Edit Aduan</h1>
        <span class="font-mono text-xs text-gray-400">{{ $complaint->aduan_no }}</span>
    </div>

    <form action="{{ route('complaints.update', $complaint) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Tajuk <span class="text-red-500">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title', $complaint->title) }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('title') ? 'border-red-400' : 'border-gray-300' }}">
            @error('title')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Penerangan <span class="text-red-500">*</span></label>
            <textarea id="description" name="description" rows="4"
                      class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('description') ? 'border-red-400' : 'border-gray-300' }}">{{ old('description', $complaint->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
            <input type="text" id="location" name="location" value="{{ old('location', $complaint->location) }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('location') ? 'border-red-400' : 'border-gray-300' }}">
            @error('location')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                <select id="category_id" name="category_id"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('category_id') ? 'border-red-400' : 'border-gray-300' }}">
                    <option value="">— Pilih Kategori —</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $complaint->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Keutamaan <span class="text-red-500">*</span></label>
                <select id="priority" name="priority"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('priority') ? 'border-red-400' : 'border-gray-300' }}">
                    <option value="low" {{ old('priority', $complaint->priority) == 'low' ? 'selected' : '' }}>Rendah</option>
                    <option value="medium" {{ old('priority', $complaint->priority) == 'medium' ? 'selected' : '' }}>Sederhana</option>
                    <option value="high" {{ old('priority', $complaint->priority) == 'high' ? 'selected' : '' }}>Tinggi</option>
                    <option value="urgent" {{ old('priority', $complaint->priority) == 'urgent' ? 'selected' : '' }}>Mendesak</option>
                </select>
                @error('priority')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Pengadu <span class="text-red-500">*</span></label>
                <select id="user_id" name="user_id"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('user_id') ? 'border-red-400' : 'border-gray-300' }}">
                    <option value="">— Pilih Pengadu —</option>
                    @foreach ($complainants as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $complaint->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="officer_id" class="block text-sm font-medium text-gray-700 mb-1">Pegawai</label>
                <select id="officer_id" name="officer_id"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('officer_id') ? 'border-red-400' : 'border-gray-300' }}">
                    <option value="">— Tiada Pegawai —</option>
                    @foreach ($officers as $officer)
                        <option value="{{ $officer->id }}" {{ old('officer_id', $complaint->officer_id) == $officer->id ? 'selected' : '' }}>
                            {{ $officer->name }}
                        </option>
                    @endforeach
                </select>
                @error('officer_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
            <select id="status" name="status"
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('status') ? 'border-red-400' : 'border-gray-300' }}">
                <option value="pending" {{ old('status', $complaint->status) == 'pending' ? 'selected' : '' }}>Menunggu</option>
                <option value="in_review" {{ old('status', $complaint->status) == 'in_review' ? 'selected' : '' }}>Dalam Semakan</option>
                <option value="in_progress" {{ old('status', $complaint->status) == 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                <option value="resolved" {{ old('status', $complaint->status) == 'resolved' ? 'selected' : '' }}>Selesai</option>
                <option value="closed" {{ old('status', $complaint->status) == 'closed' ? 'selected' : '' }}>Ditutup</option>
                <option value="rejected" {{ old('status', $complaint->status) == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
            @error('status')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                Kemaskini
            </button>
            <a href="{{ route('complaints.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 px-5 py-2">Batal</a>
        </div>
    </form>
</div>
@endsection
