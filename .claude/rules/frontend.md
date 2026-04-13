# Frontend

## Build assets
```bash
npm run build
```

## Stack
- **Tailwind CSS v4** (via `@tailwindcss/vite` plugin — no `tailwind.config.js` needed)
- **Vite 8** for asset bundling (`resources/css/app.css`, `resources/js/app.js`)

## Alerts & Confirmations

Always use **SweetAlert2** (https://github.com/sweetalert2/sweetalert2) for confirm dialogs and alerts. Never use the native `alert()`, `confirm()`, or `prompt()` browser dialogs.

```js
// confirm dialog
Swal.fire({
  title: 'Kamu Yakin?',
  text: 'Data akan dihapus.',
  icon: 'warning',
  showCancelButton: true,
  confirmButtonText: 'Ya, hapus!',
  cancelButtonText: 'Batal',
}).then((result) => {
  if (result.isConfirmed) {
    // proceed
  }
})
```

## Key files
| Path | Purpose |
|---|---|
| `resources/css/app.css` | Main stylesheet entry |
| `resources/js/app.js` | Main JavaScript entry |
