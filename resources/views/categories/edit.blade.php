@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="mb-6">
    <a href="{{ route('categories.index') }}" class="text-sm text-gray-500 hover:text-blue-600">&larr; Kembali</a>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-lg">
    <h1 class="text-xl font-bold mb-6">Edit Kategori</h1>

    <form action="{{ route('categories.update', $category) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                          {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }}">
            @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea id="description" name="description" rows="3"
                      class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                             {{ $errors->has('description') ? 'border-red-400' : 'border-gray-300' }}">{{ old('description', $category->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="is_active" name="is_active" value="1"
                   class="rounded border-gray-300 text-blue-600"
                   {{ old('is_active', $category->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
            <label for="is_active" class="text-sm text-gray-700">Aktif</label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                Perbarui
            </button>
            <a href="{{ route('categories.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 px-5 py-2">Batal</a>
            <button type="button"
                    onclick="Swal.fire({title:'Yakin?',text:'Hapus kategori ini?',icon:'warning',showCancelButton:true,confirmButtonText:'Ya, hapus!',cancelButtonText:'Batal'}).then(r=>{if(r.isConfirmed)document.getElementById('form-delete-category').submit()})"
                    class="text-red-500 hover:underline text-sm px-5 py-2 ml-auto">Hapus</button>
        </div>
    </form>

    <form id="form-delete-category" action="{{ route('categories.destroy', $category) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
