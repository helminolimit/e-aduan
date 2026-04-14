# e-Aduan — Logik E-mel Notifikasi Admin

> Modul ini menerangkan aliran penghantaran e-mel automatik kepada admin apabila data baharu ditambah ke dalam sistem.

---

## Gambaran Keseluruhan

Apabila **kategori baharu** berjaya ditambah, sistem akan menghantar e-mel notifikasi secara automatik kepada alamat e-mel admin yang dikonfigurasi melalui pembolehubah persekitaran `ADMIN_EMAIL`.

---

## Aliran Logik

```
[Borang Tambah Kategori]
        ↓
CategoryController@store
        ↓
Validasi input (name, description, is_active)
        ↓
Category::create(...)
        ↓
Semak config('mail.admin_email') — jika ada
        ↓
Mail::to($adminEmail)->send(new CategoryCreated($category))
        ↓
[E-mel dihantar kepada admin]
```

---

## Fail Terlibat

| Fail | Peranan |
|------|---------|
| `app/Http/Controllers/CategoryController.php` | Pencetus — hantar e-mel dalam method `store()` |
| `app/Mail/CategoryCreated.php` | Mailable class — membungkus data kategori untuk e-mel |
| `resources/views/emails/category_created.blade.php` | Template HTML e-mel yang dihantar kepada admin |
| `config/mail.php` | Konfigurasi e-mel termasuk `admin_email` |
| `.env` | Nilai `ADMIN_EMAIL` — alamat e-mel admin sebenar |

---

## Konfigurasi

Tambah atau kemaskini nilai berikut dalam fail `.env`:

```env
ADMIN_EMAIL="admin@syarikat.com"
```

Nilai ini dibaca melalui `config/mail.php`:

```php
'admin_email' => env('ADMIN_EMAIL'),
```

> **Nota:** Jika `ADMIN_EMAIL` tiada nilai atau tidak diisi, e-mel **tidak akan dihantar** dan tiada ralat akan berlaku.

---

## CategoryController — Method `store()`

```php
public function store(Request $request)
{
    $request->validate([
        'name'        => 'required|string|max:255|unique:categories,name',
        'description' => 'nullable|string',
        'is_active'   => 'boolean',
    ]);

    $category = Category::create([
        'name'        => $request->name,
        'description' => $request->description,
        'is_active'   => $request->boolean('is_active', true),
    ]);

    $adminEmail = config('mail.admin_email');
    if ($adminEmail) {
        Mail::to($adminEmail)->send(new CategoryCreated($category));
    }

    return redirect()->route('categories.index')
        ->with('success', 'Kategori berjaya ditambah.');
}
```

---

## Mailable — `CategoryCreated`

Kelas `App\Mail\CategoryCreated` menerima objek `Category` dan menghantar template `emails.category_created`.

```php
class CategoryCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Category $category) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kategori Baharu Telah Ditambah',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.category_created',
        );
    }
}
```

---

## Template E-mel

Fail: `resources/views/emails/category_created.blade.php`

Maklumat yang dipaparkan dalam e-mel:

| Medan | Sumber |
|-------|--------|
| Nama Kategori | `$category->name` |
| Penerangan | `$category->description` |
| Status | `$category->is_active` (Aktif / Tidak Aktif) |
| Tarikh Ditambah | `$category->created_at` |

---

## Persekitaran Pembangunan vs Pengeluaran

| Persekitaran | `MAIL_MAILER` | Tingkah Laku |
|---|---|---|
| `local` (dev) | `log` | E-mel **dilog** ke `storage/logs/laravel.log` — tidak dihantar sebenar |
| `production` | `smtp` / `mailgun` / dll. | E-mel **dihantar** kepada `ADMIN_EMAIL` |

Untuk ujian tempatan, semak log:

```bash
php artisan pail
# atau
tail -f storage/logs/laravel.log
```

---

## Panduan Pengembangan

Untuk tambah notifikasi e-mel pada modul lain (contoh: aduan baharu), ikut pola yang sama:

1. Buat Mailable baharu: `php artisan make:mail NamaMailable --markdown`
2. Buat template di `resources/views/emails/`
3. Panggil `Mail::to(...)->send(new NamaMailable(...))` dalam controller berkaitan
4. Pastikan `ADMIN_EMAIL` dikonfigurasi dalam `.env`
