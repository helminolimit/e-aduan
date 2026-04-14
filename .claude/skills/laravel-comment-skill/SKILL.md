---
name: laravel-comment-writer
description: >
  Tulis inline comment dalam Bahasa Melayu untuk kod Laravel (PHP). Gunakan skill ini apabila pengguna meminta untuk menambah komen, mendokumentasi, atau menerangkan kod dalam Controller atau Model Laravel. Trigger apabila pengguna sebut: "tambah komen", "tulis comment", "dokumentasi kod", "terangkan kod", "komen controller", "komen model", "komen function", "komen query", atau apabila pengguna paste kod Laravel dan minta penjelasan atau dokumentasi. Skill ini menghasilkan inline comment yang lengkap — menerangkan tujuan, parameter, return value dan contoh — khusus untuk method dalam Controller, validation rules, dan Eloquent query logic.
---

# Laravel Comment Writer (Bahasa Melayu)

Skill ini membantu Claude menulis **inline comment dalam Bahasa Melayu** untuk kod Laravel PHP, khususnya untuk:
- Method/function dalam **Controller**
- **Validation rules**
- **Query/Eloquent logic**

---

## Prinsip Utama

1. **Bahasa Melayu** — semua komen ditulis dalam BM yang jelas dan natural
2. **Inline comment** — guna `//` untuk satu baris, blok komen `/* */` untuk penerangan panjang
3. **Lengkap** — setiap komen mesti terangkan: tujuan, parameter, return value, dan contoh jika relevan
4. **Kontekstual** — komen mesti relevan dengan logik sebenar kod, bukan generik
5. **Tidak berlebihan** — jangan komen benda yang obvious, fokus pada logik penting

---

## Format Komen Mengikut Jenis

### 1. Method dalam Controller

```php
// [Tujuan method dalam 1 ayat ringkas]
// Parameter: $request (Request) - data yang dihantar dari borang/API
// Return: [jenis return — view, redirect, JsonResponse, dll]
// Contoh: [contoh panggilan atau situasi]
public function store(Request $request)
{
    // [komen inline untuk langkah penting dalam method]
}
```

**Panduan:**
- Komen sebelum `public function` mesti ada tujuan, parameter dan return
- Dalam badan function, komen setiap "blok logik" yang tidak jelas
- Guna komen pendek untuk baris mudah, komen panjang untuk logik kompleks

### 2. Validation Rules

```php
$request->validate([
    'nama' => 'required|string|max:255', // Nama pengguna — wajib diisi, teks sahaja, maksimum 255 aksara
    'email' => 'required|email|unique:users', // E-mel — wajib unik dalam jadual users
    'umur' => 'nullable|integer|min:18', // Umur — tidak wajib, mestilah integer, minimum 18 tahun
]);
```

**Panduan:**
- Komen inline di hujung setiap baris rule
- Terangkan KENAPA ada rule tersebut, bukan hanya apa rule itu
- Jika ada custom rule atau regex, wajib terangkan maksudnya

### 3. Eloquent Query Logic

```php
// Ambil semua pengguna aktif yang mendaftar dalam 30 hari lepas, disusun dari terbaru
$pengguna = User::where('status', 'aktif') // Tapis hanya pengguna yang status = aktif
    ->whereDate('created_at', '>=', now()->subDays(30)) // Dalam tempoh 30 hari
    ->latest() // Susun dari yang paling baru didaftarkan
    ->get();
```

**Panduan:**
- Komen blok sebelum query untuk terangkan tujuan keseluruhan
- Komen inline pada setiap chain yang tidak jelas
- Terangkan nilai/syarat yang spesifik (contoh: kenapa 30 hari, kenapa status = 'aktif')

---

## Contoh Lengkap — Controller Method

**Input (kod tanpa komen):**
```php
public function update(Request $request, $id)
{
    $request->validate([
        'nama' => 'required|string|max:100',
        'emel' => 'required|email|unique:pengguna,emel,' . $id,
        'peranan' => 'required|in:admin,pengguna,editor',
    ]);

    $pengguna = Pengguna::findOrFail($id);
    $pengguna->update($request->only(['nama', 'emel', 'peranan']));

    return redirect()->route('pengguna.index')->with('berjaya', 'Maklumat dikemaskini.');
}
```

**Output (dengan komen lengkap):**
```php
// Kemaskini maklumat pengguna berdasarkan ID yang diberikan
// Parameter: $request (Request) - data borang kemaskini, $id (int) - ID pengguna dalam pangkalan data
// Return: Redirect ke senarai pengguna dengan mesej kejayaan
// Contoh: PUT /pengguna/5 → kemaskini rekod pengguna ID 5
public function update(Request $request, $id)
{
    // Sahkan data input sebelum proses kemaskini
    $request->validate([
        'nama' => 'required|string|max:100', // Nama penuh — wajib diisi, teks sahaja, maksimum 100 aksara
        'emel' => 'required|email|unique:pengguna,emel,' . $id, // E-mel — wajib unik kecuali untuk rekod pengguna semasa
        'peranan' => 'required|in:admin,pengguna,editor', // Peranan — hanya nilai yang dibenarkan: admin, pengguna, atau editor
    ]);

    // Cari pengguna mengikut ID — akan throw 404 jika tidak dijumpai
    $pengguna = Pengguna::findOrFail($id);

    // Kemaskini hanya medan yang dibenarkan sahaja (elak mass assignment yang berbahaya)
    $pengguna->update($request->only(['nama', 'emel', 'peranan']));

    // Redirect ke halaman senarai pengguna dengan mesej kejayaan dalam session
    return redirect()->route('pengguna.index')->with('berjaya', 'Maklumat dikemaskini.');
}
```

---

## Langkah-Langkah Kerja

1. **Baca kod** — fahami tujuan sebenar setiap method, rule, dan query
2. **Kenal pasti bahagian penting** — method signatures, validation, query chains, logik bisnes
3. **Tulis komen blok** — untuk method: tujuan, parameter, return, contoh
4. **Tulis inline comment** — untuk validation rules dan baris query yang perlu penjelasan
5. **Semak semula** — pastikan komen dalam BM yang betul dan tidak redundant

---

## Benda yang TIDAK Perlu Dikomen

```php
// JANGAN komen benda obvious seperti ini:
$nama = $request->nama; // ambil nama dari request  ← TIDAK PERLU

// KECUALI ada sebab tertentu:
$nama = trim($request->nama); // buang ruang kosong di depan & belakang nama sebelum simpan  ← OK
```

---

## Rujukan Gaya Bahasa Melayu

| Istilah Teknikal | Terjemahan/Cara Tulis dalam Komen |
|---|---|
| Request | permintaan / $request |
| Return | pulangkan / kembalikan |
| Validate | sahkan / pengesahan |
| Query | pertanyaan / carian |
| Redirect | alih ke / ubah hala |
| Array | senarai / tatasusunan |
| Null / nullable | boleh kosong / tidak wajib |
| Unique | unik / tidak bertindih |
| Relationship | hubungan / kaitan |

Boleh campur istilah Inggeris jika terjemahan BM terlalu janggal (contoh: `findOrFail`, `belongsTo` boleh kekal).
