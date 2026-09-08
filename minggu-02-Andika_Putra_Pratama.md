# Laporan Praktikum / Tugas Laravel
 
**Nama:** Andika Putra Pratama
**NIM:** 10241010
 
---

### READ — Telusuri satu request penuh (30 menit)

Ambil route `/tentang` yang Anda buat minggu lalu. Tanpa AI, tulis di catatan Anda:

### 1. Baris mana di `routes/web.php` yang menangkapnya?<br>
Route `/tentang` berada di garis nomer 10 pada file `routes/web.php`.

<br>

### 2. Kalau ditangani controller, berkas dan method mana?
Route `/tentang` ditangani oleh controller di CourseController, yang file nya ada di app/Http/Controllers/CourseController.php, di method tentang().

### 3. View mana yang dikembalikan? Di path apa persisnya?
view yang dikemablikan adalah `tentang` yang mengarah ke `resources/views/tentang.blade.php`

### 4. Layout apa yang membungkusnya?
Layout yang membungkusnya adalah Blade Component <x-layout>, file nya ada di resources/views/components/layout.blade.php, dipanggil dari `tentang.blade.php` menggunakan <x-layout title="...">...</x-layout>.

### 5. Jalankan `php artisan route:list --path=tentang`. Cocok dengan analisis Anda?

Route ini menggunakan controller (`CourseController`) dengan method `tentang()`, bukan langsung ditulis sebagai closure/function di `routes/web.php` berbeda dengan route `/` dan `/dashboard` yang memang pakai closure langsung.

<img src="image/week2.jpeg">
<br>

