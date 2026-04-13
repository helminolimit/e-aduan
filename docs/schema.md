# e-Aduan — Database Schema

> Laravel soft delete digunakan pada semua table kecuali `status_logs`.
> Setiap table mempunyai `created_at`, `updated_at`, dan `deleted_at` (nullable).

---

## Table Senarai

| # | Table | Penerangan |
|---|-------|-----------|
| 1 | `users` | Semua pengguna sistem (complainant, officer, admin) |
| 2 | `categories` | Kategori aduan |
| 3 | `complaints` | Rekod aduan utama |
| 4 | `comments` | Komen dan nota dalaman pada aduan |
| 5 | `attachments` | Fail bukti yang dimuat naik |
| 6 | `notifications` | Notifikasi dalam aplikasi |
| 7 | `status_logs` | Log audit perubahan status aduan |

---

## 1. `users`

Menyimpan semua pengguna sistem merangkumi tiga peranan: `complainant`, `officer`, dan `admin`.

| Kolum | Jenis | Nullable | Penerangan |
|-------|-------|----------|-----------|
| `id` | `bigint` UNSIGNED | NO | Primary key, auto increment |
| `name` | `varchar(255)` | NO | Nama penuh pengguna |
| `email` | `varchar(255)` | NO | Emel unik pengguna |
| `password` | `varchar(255)` | NO | Kata laluan (hashed) |
| `role` | `enum('complainant','officer','admin')` | NO | Peranan pengguna dalam sistem |
| `email_verified_at` | `timestamp` | YES | Masa pengesahan emel |
| `remember_token` | `varchar(100)` | YES | Token untuk "remember me" |
| `created_at` | `timestamp` | YES | Masa rekod dicipta |
| `updated_at` | `timestamp` | YES | Masa rekod dikemaskini |
| `deleted_at` | `timestamp` | YES | Soft delete — diisi apabila pengguna dipadam |

**Migration:**
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->enum('role', ['complainant', 'officer', 'admin'])->default('complainant');
    $table->timestamp('email_verified_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();
});
```

**Model traits:** `SoftDeletes`

---

## 2. `categories`

Kategori jenis aduan seperti jalan raya, kebersihan, infrastruktur dan sebagainya.

| Kolum | Jenis | Nullable | Penerangan |
|-------|-------|----------|-----------|
| `id` | `bigint` UNSIGNED | NO | Primary key, auto increment |
| `name` | `varchar(255)` | NO | Nama kategori |
| `description` | `text` | YES | Penerangan kategori |
| `is_active` | `boolean` | NO | Status aktif kategori (default: `true`) |
| `created_at` | `timestamp` | YES | Masa rekod dicipta |
| `updated_at` | `timestamp` | YES | Masa rekod dikemaskini |
| `deleted_at` | `timestamp` | YES | Soft delete |

**Migration:**
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

**Model traits:** `SoftDeletes`

---

## 3. `complaints`

Table utama sistem. Menyimpan semua aduan yang dikemukakan oleh orang awam.

| Kolum | Jenis | Nullable | Penerangan |
|-------|-------|----------|-----------|
| `id` | `bigint` UNSIGNED | NO | Primary key, auto increment |
| `aduan_no` | `varchar(20)` | NO | Nombor rujukan aduan (unik, contoh: `ADU-2024-00001`) |
| `user_id` | `bigint` UNSIGNED | NO | FK → `users.id` (pengadu) |
| `category_id` | `bigint` UNSIGNED | NO | FK → `categories.id` |
| `officer_id` | `bigint` UNSIGNED | YES | FK → `users.id` (pegawai yang ditugaskan) |
| `title` | `varchar(255)` | NO | Tajuk aduan |
| `description` | `text` | NO | Penerangan terperinci aduan |
| `location` | `varchar(500)` | NO | Lokasi kejadian |
| `status` | `enum(...)` | NO | Status semasa aduan (lihat nilai di bawah) |
| `priority` | `enum('low','medium','high','urgent')` | NO | Tahap keutamaan (default: `medium`) |
| `created_at` | `timestamp` | YES | Masa aduan dikemukakan |
| `updated_at` | `timestamp` | YES | Masa rekod dikemaskini |
| `deleted_at` | `timestamp` | YES | Soft delete |

**Nilai enum `status`:** `pending`, `in_review`, `in_progress`, `resolved`, `closed`, `rejected`

**Migration:**
```php
Schema::create('complaints', function (Blueprint $table) {
    $table->id();
    $table->string('aduan_no', 20)->unique();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('category_id')->constrained('categories');
    $table->foreignId('officer_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('title');
    $table->text('description');
    $table->string('location', 500);
    $table->enum('status', ['pending','in_review','in_progress','resolved','closed','rejected'])
          ->default('pending');
    $table->enum('priority', ['low','medium','high','urgent'])->default('medium');
    $table->timestamps();
    $table->softDeletes();
});
```

**Model traits:** `SoftDeletes`

---

## 4. `comments`

Komen yang ditambah pada aduan. Boleh bersifat awam (boleh dilihat pengadu) atau dalaman (officer sahaja).

| Kolum | Jenis | Nullable | Penerangan |
|-------|-------|----------|-----------|
| `id` | `bigint` UNSIGNED | NO | Primary key, auto increment |
| `complaint_id` | `bigint` UNSIGNED | NO | FK → `complaints.id` |
| `user_id` | `bigint` UNSIGNED | NO | FK → `users.id` (penulis komen) |
| `content` | `text` | NO | Kandungan komen |
| `is_internal` | `boolean` | NO | `true` = nota dalaman officer sahaja (default: `false`) |
| `created_at` | `timestamp` | YES | Masa komen dicipta |
| `updated_at` | `timestamp` | YES | Masa rekod dikemaskini |
| `deleted_at` | `timestamp` | YES | Soft delete |

**Migration:**
```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->text('content');
    $table->boolean('is_internal')->default(false);
    $table->timestamps();
    $table->softDeletes();
});
```

**Model traits:** `SoftDeletes`

---

## 5. `attachments`

Fail bukti (gambar, dokumen) yang dimuat naik bersama aduan.

| Kolum | Jenis | Nullable | Penerangan |
|-------|-------|----------|-----------|
| `id` | `bigint` UNSIGNED | NO | Primary key, auto increment |
| `complaint_id` | `bigint` UNSIGNED | NO | FK → `complaints.id` |
| `file_path` | `varchar(500)` | NO | Laluan fail dalam storage |
| `file_name` | `varchar(255)` | NO | Nama asal fail |
| `mime_type` | `varchar(100)` | NO | Jenis fail (contoh: `image/jpeg`, `application/pdf`) |
| `file_size` | `bigint` UNSIGNED | NO | Saiz fail dalam bytes |
| `created_at` | `timestamp` | YES | Masa fail dimuat naik |
| `updated_at` | `timestamp` | YES | Masa rekod dikemaskini |
| `deleted_at` | `timestamp` | YES | Soft delete |

**Migration:**
```php
Schema::create('attachments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
    $table->string('file_path', 500);
    $table->string('file_name');
    $table->string('mime_type', 100);
    $table->unsignedBigInteger('file_size');
    $table->timestamps();
    $table->softDeletes();
});
```

**Model traits:** `SoftDeletes`

---

## 6. `notifications`

Notifikasi dalam aplikasi yang dihantar kepada pengguna apabila terdapat aktiviti pada aduan mereka.

| Kolum | Jenis | Nullable | Penerangan |
|-------|-------|----------|-----------|
| `id` | `bigint` UNSIGNED | NO | Primary key, auto increment |
| `user_id` | `bigint` UNSIGNED | NO | FK → `users.id` (penerima notifikasi) |
| `complaint_id` | `bigint` UNSIGNED | YES | FK → `complaints.id` (aduan berkaitan) |
| `type` | `varchar(100)` | NO | Jenis notifikasi (contoh: `status_updated`, `comment_added`) |
| `message` | `text` | NO | Kandungan mesej notifikasi |
| `is_read` | `boolean` | NO | Status bacaan (default: `false`) |
| `created_at` | `timestamp` | YES | Masa notifikasi dicipta |
| `updated_at` | `timestamp` | YES | Masa rekod dikemaskini |
| `deleted_at` | `timestamp` | YES | Soft delete |

**Migration:**
```php
Schema::create('notifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('complaint_id')->nullable()->constrained('complaints')->nullOnDelete();
    $table->string('type', 100);
    $table->text('message');
    $table->boolean('is_read')->default(false);
    $table->timestamps();
    $table->softDeletes();
});
```

**Model traits:** `SoftDeletes`

---

## 7. `status_logs`

Audit trail setiap perubahan status pada aduan. Table ini **tidak menggunakan soft delete** untuk mengekalkan integriti rekod sejarah.

| Kolum | Jenis | Nullable | Penerangan |
|-------|-------|----------|-----------|
| `id` | `bigint` UNSIGNED | NO | Primary key, auto increment |
| `complaint_id` | `bigint` UNSIGNED | NO | FK → `complaints.id` |
| `changed_by` | `bigint` UNSIGNED | NO | FK → `users.id` (yang membuat perubahan) |
| `old_status` | `varchar(50)` | YES | Status sebelum perubahan |
| `new_status` | `varchar(50)` | NO | Status selepas perubahan |
| `remarks` | `text` | YES | Catatan atau sebab perubahan |
| `created_at` | `timestamp` | YES | Masa perubahan dibuat |
| `updated_at` | `timestamp` | YES | Masa rekod dikemaskini |

**Migration:**
```php
Schema::create('status_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
    $table->foreignId('changed_by')->constrained('users');
    $table->string('old_status', 50)->nullable();
    $table->string('new_status', 50);
    $table->text('remarks')->nullable();
    $table->timestamps();
    // Tiada softDeletes() — audit trail mesti kekal
});
```

> **Nota:** `status_logs` tidak mempunyai `deleted_at`. Rekod log tidak boleh dipadam kerana ia adalah audit trail yang diperlukan untuk pelaporan dan akauntabiliti.

---

## Ringkasan Soft Delete

| Table | Soft Delete | Sebab |
|-------|-------------|-------|
| `users` | ✅ Ya | Pengguna boleh dinyahaktifkan tanpa hilang data aduan |
| `categories` | ✅ Ya | Kategori lama boleh disembunyikan tanpa padam |
| `complaints` | ✅ Ya | Aduan diarkib, bukan dipadam terus |
| `comments` | ✅ Ya | Komen boleh dipadam tapi boleh dipulihkan |
| `attachments` | ✅ Ya | Fail boleh dipadam tapi rekod metadata kekal |
| `notifications` | ✅ Ya | Notifikasi boleh dikosongkan dengan selamat |
| `status_logs` | ❌ Tidak | Audit trail mesti kekal sepenuhnya |

---

## Cara Guna Soft Delete dalam Model

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use SoftDeletes;
}
```

```php
// Padam (soft delete — isi deleted_at)
$complaint->delete();

// Query termasuk rekod yang dipadam
Complaint::withTrashed()->get();

// Query rekod yang dipadam sahaja
Complaint::onlyTrashed()->get();

// Pulihkan rekod yang dipadam
$complaint->restore();

// Padam kekal dari database
$complaint->forceDelete();
```
