# Catatan Minggu 02
## Nama: Alya Auralia
## NIM: 10241008
## Mata Kuliah: Pemrograman Web B

# Read - Telusuri satu request penuh
### 1. Baris mana di `routes/web.php` yang menangkapnya? <Br>
Jawaban= <Br>
Baris yang menangkap route `/tentang` ada berada pada baris 10, yaitu:
```php
Route::get('/tentang', [CourseController::class, 'tentang']);
```
<Br>

- `Route::get('/tentang', ...)`: menangkap request `GET` ke alamat `/tentang`.
- `[CourseController::class, 'tentang']`: request itu diserahkan ke method `tentang()` di dalam fie CourseController.php yang yang menentukan response apa yang dikembalikan ke browser

### 2. Kalau ditangani controller, berkas dan method mana? <Br>
Jawaban= <Br>
Karena route-nya ditulis `[CourseController::class, 'tentang']`, artinya Laravel akan membuka class CourseController yang lokasinya di berkas `app/Http/Controllers/CourseController.php`, lalu menjalankan method bernama `tentang()` di dalamnya. Isi method itulah yang menentukan response apa yang dikembalikan ke browser yaitu `return view(...).`

### 3. View mana yang dikembalikan? Di path apa persisnya? <Br>
Jawaban= <Br>
View yang dikembalikan adalah `tentang`, di path `resources/views/tentang.blade.php`

### 4. Layout apa yang membungkusnya? <Br>
Jawaban= <Br>
Layout yang membungkusnya adalah `<x-layout>`, sebuah Blade Component, yang file-nya ada di path: `resources/views/components/layout.blade.php` 

### 5. Jalankan `php artisan route:list --path=tentang`. Cocok dengan analisis Anda? <Br>
Jawaban= <Br>
Setelah dijalankan, hasilnya sesuai dengan analisis sebelumnya yaitu: <Br>
- Method: `GET|HEAD` → sesuai `Route::get(...)`
- URI: `tentang` → sesuai` /tentang` yang dianalisis
- Lokasi: `routes/web.php:10` → baris ke-10 di file tersebut
- Total: `Showing [1] routes` → cuma ada 1 route yang cocok, tidak ada bentrok

berikut hasil yang ditampilkan:<Br>

<img src="image/hasil terminal.jpeg">