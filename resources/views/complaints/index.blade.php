@extends('layouts.app')

@section('title', 'Senarai Aduan')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Senarai Aduan</h1>
    <a href="{{ route('complaints.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Tambah Aduan
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600 w-8">#</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">No. Aduan</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Tajuk</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Kategori</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Keutamaan</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Tarikh</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse ($complaints as $complaint)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400">{{ $complaints->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $complaint->aduan_no }}</td>
                    <td class="px-4 py-3 font-medium">{{ $complaint->title }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $complaint->category->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @php
                            $statusClasses = [
                                'pending'     => 'bg-yellow-100 text-yellow-700',
                                'in_review'   => 'bg-blue-100 text-blue-700',
                                'in_progress' => 'bg-indigo-100 text-indigo-700',
                                'resolved'    => 'bg-green-100 text-green-700',
                                'closed'      => 'bg-gray-100 text-gray-600',
                                'rejected'    => 'bg-red-100 text-red-600',
                            ];
                            $statusLabels = [
                                'pending'     => 'Menunggu',
                                'in_review'   => 'Dalam Semakan',
                                'in_progress' => 'Dalam Proses',
                                'resolved'    => 'Selesai',
                                'closed'      => 'Ditutup',
                                'rejected'    => 'Ditolak',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusClasses[$complaint->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $statusLabels[$complaint->status] ?? $complaint->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $priorityClasses = [
                                'low'    => 'bg-gray-100 text-gray-600',
                                'medium' => 'bg-blue-100 text-blue-600',
                                'high'   => 'bg-orange-100 text-orange-600',
                                'urgent' => 'bg-red-100 text-red-600',
                            ];
                            $priorityLabels = [
                                'low'    => 'Rendah',
                                'medium' => 'Sederhana',
                                'high'   => 'Tinggi',
                                'urgent' => 'Mendesak',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $priorityClasses[$complaint->priority] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $priorityLabels[$complaint->priority] ?? $complaint->priority }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-400">{{ $complaint->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                        <a href="{{ route('complaints.edit', $complaint) }}"
                           class="text-blue-600 hover:underline text-xs">Edit</a>
                        <form action="{{ route('complaints.destroy', $complaint) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                    onclick="Swal.fire({title:'Kamu Yakin?',text:'Aduan ini akan dipadam.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya, padam!',cancelButtonText:'Batal'}).then(r=>{if(r.isConfirmed)this.closest('form').submit()})"
                                    class="text-red-500 hover:underline text-xs">Padam</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">Tiada aduan dijumpai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($complaints->hasPages())
    <div class="mt-4">{{ $complaints->links() }}</div>
@endif
@endsection
