# Troubleshooting - File Upload & Preview

## Problem: File Tidak Terload di Modal Preview

### Diagnosa

Jika file tidak terload, cek hal-hal berikut:

#### 1. Cek Storage Link

Pastikan symbolic link dari `public/storage` ke `storage/app/public` sudah dibuat:

```bash
php artisan storage:link
```

**Output yang benar:**
```
The [public/storage] link has been connected to [storage/app/public].
The links have been created.
```

#### 2. Cek Path File di Database

Buka database dan cek tabel `dokumen_pengajuan`, kolom `path_file`.

Path yang **BENAR**:
```
✅ dokumen/kgb/filename.pdf
✅ uploads/2025/11/filename.jpg
✅ kgb/pengajuan_1/sk_pangkat.pdf
```

Path yang **SALAH** (jangan include 'public/' atau 'storage/'):
```
❌ public/dokumen/kgb/filename.pdf
❌ storage/app/public/dokumen/filename.pdf
❌ /var/www/storage/dokumen/filename.pdf
```

#### 3. Cek File Exists di Server

Jalankan command berikut di terminal server:

```bash
# Cek apakah file ada
ls -la storage/app/public/dokumen/kgb/

# Atau
php artisan tinker
>>> Storage::disk('public')->exists('dokumen/kgb/filename.pdf')
```

#### 4. Cek Permission Folder

Pastikan folder storage punya permission yang benar:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Jika menggunakan www-data user
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
```

#### 5. Cek Console Browser

Buka Chrome DevTools (F12) > Console tab, cek error message:

```javascript
// Harusnya muncul log seperti ini:
Modal opened with URL: http://localhost/storage/dokumen/kgb/filename.pdf
Is Image: false

// Jika ada error CORS:
Access to XMLHttpRequest blocked by CORS policy

// Jika ada error 404:
GET http://localhost/storage/dokumen/kgb/filename.pdf 404 (Not Found)
```

### Solusi Berdasarkan Error

#### Error: "File tidak ditemukan" (Path: xxx)

**Penyebab:** File path di database tidak sesuai dengan lokasi file aktual.

**Solusi:**

1. **Cek format path di database:**
   ```sql
   SELECT id, nama_file, path_file FROM dokumen_pengajuan WHERE pengajuan_kgb_id = 6;
   ```

2. **Update path jika salah:**
   ```sql
   -- Jika path_file ada 'public/' prefix, hapus:
   UPDATE dokumen_pengajuan 
   SET path_file = REPLACE(path_file, 'public/', '') 
   WHERE path_file LIKE 'public/%';
   
   -- Atau update manual:
   UPDATE dokumen_pengajuan 
   SET path_file = 'dokumen/kgb/filename.pdf' 
   WHERE id = 1;
   ```

#### Error: 404 di Console Browser

**Penyebab:** Symbolic link belum dibuat atau path URL salah.

**Solusi:**

```bash
# Hapus link lama jika ada
rm public/storage

# Buat link baru
php artisan storage:link

# Verifikasi link
ls -la public/ | grep storage
```

#### Error: CORS Policy

**Penyebab:** File dari domain berbeda atau protocol berbeda (http vs https).

**Solusi:**

Tambahkan di `config/cors.php`:

```php
'paths' => ['api/*', 'storage/*', 'sanctum/csrf-cookie'],
```

#### Error: Permission Denied

**Penyebab:** Folder storage tidak punya permission yang tepat.

**Solusi:**

```bash
# Set permission
chmod -R 775 storage

# Set owner (sesuaikan dengan web server user)
sudo chown -R www-data:www-data storage

# Atau untuk development
sudo chown -R $USER:$USER storage
```

### Best Practices untuk Upload File

#### 1. Simpan File dengan Path Relatif

```php
// BENAR ✅
$path = $request->file('dokumen')->store('dokumen/kgb', 'public');
// Hasil: dokumen/kgb/abc123.pdf

$dokumen->path_file = $path; // Simpan path relatif
$dokumen->save();

// SALAH ❌
$path = $request->file('dokumen')->store('dokumen/kgb', 'public');
$dokumen->path_file = 'public/' . $path; // Jangan tambah prefix!
```

#### 2. Generate URL Saat Akses

```php
// Di blade view atau controller
$fileUrl = Storage::disk('public')->url($dokumen->path_file);

// Atau dengan accessor di Model
public function getFileUrlAttribute()
{
    return Storage::disk('public')->url($this->path_file);
}

// Usage: $dokumen->file_url
```

#### 3. Validasi File Upload

```php
$request->validate([
    'dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // max 5MB
]);
```

### Testing File Upload & Preview

#### Test Script

Jalankan di `php artisan tinker`:

```php
// Test 1: Cek file exists
$dokumen = \App\Models\DokumenPengajuan::first();
Storage::disk('public')->exists($dokumen->path_file);
// Harusnya return: true

// Test 2: Generate URL
$url = Storage::disk('public')->url($dokumen->path_file);
echo $url;
// Harusnya return: http://localhost/storage/dokumen/kgb/filename.pdf

// Test 3: Cek file size
$size = Storage::disk('public')->size($dokumen->path_file);
echo $size . ' bytes';

// Test 4: List files di folder
Storage::disk('public')->files('dokumen/kgb');
```

#### Manual Test via Browser

Buka URL langsung di browser:

```
http://localhost/storage/dokumen/kgb/filename.pdf
```

Jika file tidak bisa diakses:
1. Cek `public/storage` symbolic link ada
2. Cek file ada di `storage/app/public/dokumen/kgb/`
3. Cek permission folder

### Debug Mode

Aktifkan debug di DokumenList component dengan melihat log:

```bash
# Watch log file
tail -f storage/logs/laravel.log

# Atau filter khusus file checking
tail -f storage/logs/laravel.log | grep "Checking file path"
```

**Log Output yang Benar:**
```
[2025-11-15 09:15:00] local.INFO: Checking file path: dokumen/kgb/filename.pdf
[2025-11-15 09:15:00] local.INFO: File found in public disk: http://localhost/storage/dokumen/kgb/filename.pdf
[2025-11-15 09:15:00] local.INFO: Dispatched modal event {"url":"http://localhost/storage/dokumen/kgb/filename.pdf","isImage":false,"fileName":"SK Pangkat.pdf"}
```

**Log Output Error:**
```
[2025-11-15 09:15:00] local.INFO: Checking file path: public/dokumen/kgb/filename.pdf
[2025-11-15 09:15:00] local.ERROR: File not found in any location: public/dokumen/kgb/filename.pdf
```

### Quick Fix Script

Buat file `fix-dokumen-path.php` di root project:

```php
<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DokumenPengajuan;
use Illuminate\Support\Facades\Storage;

$dokumens = DokumenPengajuan::all();

foreach ($dokumens as $dokumen) {
    echo "Checking: {$dokumen->id} - {$dokumen->path_file}\n";
    
    if (Storage::disk('public')->exists($dokumen->path_file)) {
        echo "  ✅ OK\n";
    } else {
        echo "  ❌ NOT FOUND\n";
        
        // Try fix
        if (str_starts_with($dokumen->path_file, 'public/')) {
            $newPath = str_replace('public/', '', $dokumen->path_file);
            if (Storage::disk('public')->exists($newPath)) {
                echo "  🔧 Fixed: {$newPath}\n";
                $dokumen->path_file = $newPath;
                $dokumen->save();
            }
        }
    }
}

echo "\nDone!\n";
```

Jalankan:
```bash
php fix-dokumen-path.php
```

### Environment Configuration

Cek file `.env`:

```env
# Pastikan setting ini ada
APP_URL=http://localhost  # atau domain Anda
FILESYSTEM_DISK=public
```

### Common Patterns

| Upload Method | Path Tersimpan | URL Akses |
|---------------|----------------|----------|
| `->store('dokumen', 'public')` | `dokumen/xxx.pdf` | `/storage/dokumen/xxx.pdf` |
| `->storeAs('dokumen', 'file.pdf', 'public')` | `dokumen/file.pdf` | `/storage/dokumen/file.pdf` |
| `->putFileAs('dokumen', $file, 'name.pdf', 'public')` | `dokumen/name.pdf` | `/storage/dokumen/name.pdf` |

**Key Point:** Path di database **TIDAK** boleh include `public/` prefix!

---

**Last Updated:** 15 November 2025, 17:15 WITA
