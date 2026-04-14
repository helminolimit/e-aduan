# Code Review — Complaints CRUD

**Tarikh:** 2026-04-14
**Skop:** `ComplaintController`, views `complaints/`, `routes/web.php`
**Pengulas:** laravel-code-reviewer agent

---

## Ringkasan

Struktur asas CRUD adalah baik — eager loading, route model binding, SweetAlert2, dan teks Malay semua betul. Namun ada beberapa isu kritikal yang perlu diperbaiki sebelum production, terutamanya race condition pada `aduan_no` dan ketiadaan authorization.

---

## Isu Kritikal (Mesti Diperbaiki)

### 1. Race Condition dalam `aduan_no` Generator

**Fail:** `app/Http/Controllers/ComplaintController.php` baris 43–47

Dua request serentak boleh membaca rekod `$last` yang sama dan menjana nombor yang sama, melanggar kekangan `unique` dan menyebabkan `500`. Logik penjanaan mesti dibungkus dalam `DB::transaction()` dengan kunci peringkat pangkalan data.

```php
use Illuminate\Support\Facades\DB;

$aduanNo = DB::transaction(function () {
    $year   = now()->format('Y');
    $prefix = 'ADU-' . $year . '-';

    // Kunci baris terkini supaya request serentak beratur
    $last = Complaint::withTrashed()
                ->where('aduan_no', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderByDesc('aduan_no')
                ->first();

    $sequence = $last ? ((int) substr($last->aduan_no, strlen($prefix))) + 1 : 1;

    return $prefix . sprintf('%05d', $sequence);
});

$complaint           = new Complaint($validated);
$complaint->aduan_no = $aduanNo;
$complaint->save();
```

> **Nota:** `withTrashed()` penting — rekod yang dipadam (soft-deleted) masih menduduki slot urutan dan mesti dikira untuk mengelakkan pertindihan.

---

### 2. Tiada Authorization

**Fail:** `routes/web.php`

Mana-mana pengguna tanpa login boleh create, edit, dan delete aduan. Route tiada middleware `auth`, dan tiada semakan Policy atau Gate dalam controller.

```php
// routes/web.php — bungkus dengan auth middleware
Route::middleware('auth')->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('comments',   CommentController::class)->except(['show']);
    Route::resource('complaints', ComplaintController::class)->except(['show']);
});
```

Kemudian buat Policy untuk kawalan berasaskan peranan:

```bash
php artisan make:policy ComplaintPolicy --model=Complaint
```

---

### 3. `aduan_no` Boleh Di-override Melalui Mass Assignment

**Fail:** `app/Models/Complaint.php` baris 18

`aduan_no` ada dalam `$fillable`. Request yang diubah suai boleh membekalkan nilai sewenang-wenangnya dan mengatasi nombor rujukan yang dijana. Ia mesti dibuang dari `$fillable` dan ditetapkan terus.

```php
// Model: buang 'aduan_no' dari $fillable
protected $fillable = [
    'user_id', 'category_id', 'officer_id',
    'title', 'description', 'location', 'status', 'priority',
];
```

---

## Penambahbaikan Penting (Patut Diperbaiki)

### 1. Validation Diduplikasi di `store` dan `update`

**Fail:** `ComplaintController.php` baris 32–41 dan 76–85

Set peraturan yang sama muncul dua kali. Ekstrak ke Form Request:

```bash
php artisan make:request StoreComplaintRequest
php artisan make:request UpdateComplaintRequest
```

```php
// app/Http/Requests/StoreComplaintRequest.php
use Illuminate\Validation\Rule;

public function rules(): array
{
    return [
        'user_id'     => ['required', 'exists:users,id'],
        'category_id' => ['required', 'exists:categories,id'],
        'officer_id'  => ['nullable', 'exists:users,id'],
        'title'       => ['required', 'string', 'max:255'],
        'description' => ['required', 'string'],
        'location'    => ['required', 'string', 'max:500'],
        'status'      => ['required', Rule::in(['pending','in_review','in_progress','resolved','closed','rejected'])],
        'priority'    => ['required', Rule::in(['low','medium','high','urgent'])],
    ];
}
```

---

### 2. Label/Warna Status dan Keutamaan Disalin-tampal di 3 View

**Fail:** `complaints/index.blade.php`, `create.blade.php`, `edit.blade.php`

Peta label dan warna diduplikasi merentas semua view. Ekstrak ke Blade component:

```bash
php artisan make:component ComplaintStatusBadge --view
php artisan make:component ComplaintPriorityBadge --view
```

Atau simpan dalam fail config/helper bersama:

```php
// app/Support/ComplaintLabels.php
class ComplaintLabels
{
    public static array $status = [
        'pending'     => ['label' => 'Menunggu',      'class' => 'bg-yellow-100 text-yellow-700'],
        'in_review'   => ['label' => 'Dalam Semakan', 'class' => 'bg-blue-100 text-blue-700'],
        'in_progress' => ['label' => 'Dalam Proses',  'class' => 'bg-indigo-100 text-indigo-700'],
        'resolved'    => ['label' => 'Selesai',        'class' => 'bg-green-100 text-green-700'],
        'closed'      => ['label' => 'Ditutup',        'class' => 'bg-gray-100 text-gray-600'],
        'rejected'    => ['label' => 'Ditolak',        'class' => 'bg-red-100 text-red-600'],
    ];

    public static array $priority = [
        'low'    => ['label' => 'Rendah',   'class' => 'bg-gray-100 text-gray-600'],
        'medium' => ['label' => 'Sederhana','class' => 'bg-blue-100 text-blue-600'],
        'high'   => ['label' => 'Tinggi',   'class' => 'bg-orange-100 text-orange-600'],
        'urgent' => ['label' => 'Mendesak', 'class' => 'bg-red-100 text-red-600'],
    ];
}
```

---

### 3. Status Boleh Ditetapkan Oleh Pengadu Semasa Mencipta Aduan

**Fail:** `resources/views/complaints/create.blade.php` baris 109–123

Membenarkan penghantar menetapkan `status: resolved` atau `status: rejected` pada aduan baru memintas keseluruhan aliran kerja. Status mesti dikodkan keras kepada `pending` semasa penciptaan dan medan ini dibuang dari form create.

```php
// store() — buang 'status' dari validated dan paksa 'pending'
$complaint->status = 'pending';
```

Buang `<select name="status">` sepenuhnya dari `create.blade.php`.

---

### 4. `user_id` Sepatutnya Default kepada Pengguna yang Login

**Fail:** `resources/views/complaints/create.blade.php` baris 77–89

Mendedahkan dropdown semua pengadu membolehkan mana-mana pengguna memfailkan aduan bagi pihak orang lain. Untuk pengguna biasa, `user_id` mesti ditetapkan kepada `auth()->id()` di server dan dropdown disembunyikan.

```php
// store() dalam controller
'user_id' => auth()->id(),
```

---

### 5. Guna `$request->validated()` Bukan `$request->xxx`

**Fail:** `ComplaintController.php` baris 49–58 dan 87–96

Selepas memanggil `$request->validate()`, data yang disahkan harus diambil melalui `$request->validated()` supaya hanya medan bersih yang dihantar ke `create()`/`update()`.

```php
$validated = $request->validated();
Complaint::create($validated);
```

---

### 6. Flash Message `CategoryController` Pakai Bahasa Indonesia

**Fail:** `app/Http/Controllers/CategoryController.php` baris 86 dan 98

Menggunakan `'berhasil diperbarui'` dan `'berhasil dihapus'` — ini Bahasa Indonesia. Patut guna Bahasa Melayu:

```php
->with('success', 'Kategori berjaya dikemaskini.')  // update
->with('success', 'Kategori berjaya dipadam.')       // destroy
```

---

### 7. Tiada Index pada Kolum `status` dan `priority`

**Fail:** `database/migrations/2024_01_01_000002_create_complaints_table.php`

Kolum `status` dan `priority` hampir pasti akan digunakan dalam klausa `WHERE` untuk penapisan. Tambah index:

```php
$table->index('status');
$table->index('priority');
$table->index(['status', 'priority']); // komposit untuk penapisan gabungan
```

---

## Cadangan (Nice to Have)

- Guna `now()->format('Y')` ganti `date('Y')` untuk timezone-aware dengan Carbon
- Extract inline SweetAlert2 `onclick` di `index.blade.php` ke fungsi JS bernama dalam `app.js` supaya boleh digunakan semula
- Tambah `show` method, route, dan view — perlu untuk melihat penerangan penuh, sejarah status, komen, dan lampiran
- Pertimbangkan PHP 8.1 backed Enum untuk `status` dan `priority` — menjadikan peta label sebagai method pada enum
- Kolum `category_id` tiada `cascadeOnDelete` atau `restrictOnDelete` yang eksplisit — pertimbangkan sama ada memadam kategori harus disekat atau menull-kan kolum pada aduan berkaitan

---

## Perkara Positif

- Eager loading (`with(['complainant', 'category', 'officer'])`) digunakan dengan betul pada index — tiada N+1
- Route model binding digunakan pada `edit`, `update`, dan `destroy` — bersih dan betul
- `SoftDeletes` ada pada model dan migration — aduan yang dipadam boleh dipulihkan
- `@csrf` dan `@method('DELETE')` kedua-duanya ada pada form padam
- Semua teks UI secara konsisten dalam Bahasa Melayu termasuk label badge status/keutamaan
- SweetAlert2 digunakan dengan betul untuk pengesahan padam — `confirm()` natif dielakkan
- Nilai fallback `old()` diwayarkan pada setiap medan form dalam view `create` dan `edit`
- Nombor baris halaman (`firstItem() + $loop->index`) adalah pendekatan yang betul berbanding `$loop->iteration` naif

---

## Senarai Semak Tindakan

| Keutamaan | Tindakan |
|-----------|----------|
| KRITIKAL | Bungkus jana `aduan_no` dalam `DB::transaction()` + `lockForUpdate()` + `withTrashed()` |
| KRITIKAL | Tambah `auth` middleware ke semua resource routes |
| KRITIKAL | Buang `aduan_no` dari `$fillable`, assign terus sebelum `save()` |
| TINGGI | Buat `StoreComplaintRequest` & `UpdateComplaintRequest` |
| TINGGI | Guna `$request->validated()` dalam `store()` & `update()` |
| TINGGI | Buang `status` select dari `create.blade.php`, hardcode `pending` |
| TINGGI | Set `user_id = auth()->id()` dalam `store()` |
| TINGGI | Buat `ComplaintPolicy` dan autorisasi `update`/`destroy` |
| SEDERHANA | Extract badge status/keutamaan ke Blade component |
| SEDERHANA | Betulkan flash messages di `CategoryController` → guna `berjaya` |
| SEDERHANA | Tambah DB indexes untuk `status` & `priority` |
| RENDAH | Guna `now()->format('Y')` ganti `date('Y')` |
| RENDAH | Extract SweetAlert2 `onclick` ke fungsi JS dalam `app.js` |
| RENDAH | Tambah `show` view untuk detail penuh aduan |
