<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Kategori Baharu Ditambah</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2>Kategori Baharu Telah Ditambah</h2>

    <p>Salam Admin,</p>

    <p>Satu kategori baharu telah berjaya ditambah ke dalam sistem <strong>{{ config('app.name') }}</strong>.</p>

    <table style="border-collapse: collapse; width: 100%; max-width: 480px;">
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9; width: 140px;"><strong>Nama Kategori</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{ $category->name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9;"><strong>Penerangan</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{ $category->description ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9;"><strong>Status</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{ $category->is_active ? 'Aktif' : 'Tidak Aktif' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9;"><strong>Tarikh Ditambah</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{ $category->created_at->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <p style="margin-top: 24px; color: #888; font-size: 12px;">
        Ini adalah e-mel automatik daripada sistem {{ config('app.name') }}. Sila jangan balas e-mel ini.
    </p>
</body>
</html>
