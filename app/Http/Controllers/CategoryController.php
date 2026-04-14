<?php

namespace App\Http\Controllers;

use App\Mail\CategoryCreated;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CategoryController extends Controller
{
    // Paparkan senarai semua kategori dengan pagination
    // Return: View 'categories.index' dengan senarai kategori (10 rekod setiap halaman)
    public function index()
    {
        // Ambil semua kategori, disusun dari yang terbaru, dengan 10 rekod setiap halaman
        $categories = Category::latest()->paginate(10);

        return view('categories.index', compact('categories'));
    }

    // Paparkan borang untuk mencipta kategori baru
    // Return: View 'categories.create'
    public function create()
    {
        return view('categories.create');
    }

    // Simpan kategori baru ke pangkalan data dan hantar notifikasi e-mel kepada admin
    // Parameter: $request (Request) - data borang termasuk nama, penerangan dan status aktif
    // Return: Redirect ke senarai kategori dengan mesej kejayaan
    public function store(Request $request)
    {
        // Sahkan data input sebelum menyimpan kategori baru
        $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name', // Nama kategori — wajib diisi, unik dalam jadual categories, maksimum 255 aksara
            'description' => 'nullable|string', // Penerangan — tidak wajib, teks bebas
            'is_active'   => 'boolean', // Status aktif — mestilah nilai boolean (true/false)
        ]);

        // Cipta rekod kategori baru dalam pangkalan data
        $category = Category::create([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true), // Lalai kepada true jika medan tidak dihantar
        ]);

        // Hantar notifikasi e-mel kepada admin jika alamat e-mel admin dikonfigurasi
        $adminEmail = config('mail.admin_email');
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new CategoryCreated($category));
        }

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berjaya ditambah.');
    }

    // Paparkan borang untuk mengemaskini kategori sedia ada
    // Parameter: $category (Category) - rekod kategori yang akan diedit (auto-resolve via route model binding)
    // Return: View 'categories.edit' dengan data kategori semasa
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    // Kemaskini maklumat kategori dalam pangkalan data
    // Parameter: $request (Request) - data borang kemaskini, $category (Category) - rekod kategori sasaran
    // Return: Redirect ke senarai kategori dengan mesej kejayaan
    public function update(Request $request, Category $category)
    {
        // Sahkan data input — nama mesti unik kecuali untuk rekod kategori semasa (elak konflik dengan dirinya sendiri)
        $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id, // Nama kategori — wajib unik, dikecualikan ID semasa supaya tidak konflik semasa kemaskini
            'description' => 'nullable|string', // Penerangan — tidak wajib, teks bebas
            'is_active'   => 'boolean', // Status aktif — mestilah nilai boolean
        ]);

        // Kemaskini rekod kategori dengan data baru dari borang
        $category->update([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true), // Lalai kepada true jika medan tidak dihantar
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    // Padam rekod kategori dari pangkalan data
    // Parameter: $category (Category) - rekod kategori yang akan dipadam (auto-resolve via route model binding)
    // Return: Redirect ke senarai kategori dengan mesej kejayaan
    public function destroy(Category $category)
    {
        // Padam rekod kategori secara kekal dari pangkalan data
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
