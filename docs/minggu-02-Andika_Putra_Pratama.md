# Laporan Praktikum / Tugas Laravel
 
**Nama:** Andika Putra Pratama
**NIM:** 10241010
 
---

### READ — Telusuri satu request penuh (30 menit)

Ambil route `/tentang` yang Anda buat minggu lalu. Tanpa AI, tulis di catatan Anda:

### 1. Baris mana di `routes/web.php` yang menangkapnya?<br>
Route `/tentang` berada di garis nomer 9 pada file `routes/web.php`.

<br> 

### 2. Kalau ditangani controller, berkas dan method mana?
tidak ditangani oleh controller karena di file `routes/web.php` tepatnya di line 9 route langsung berisi fungsi tidak memanggil controller

### 3. View mana yang dikembalikan? Di path apa persisnya?
view yang dikemablikan adalah `tentang` yang mengarah ke `resources/views/tentang.blade.php`

### 4. Layout apa yang membungkusnya?
tidak ada layout yang membungkusnya pada file `tentang.blade.php` hanya berisikan code html

### 5. Jalankan `php artisan route:list --path=tentang`. Cocok dengan analisis Anda?

sesuai dengan analisa saya route `/tentang` terdapat di baris 9 dan juga menggunakan method GET. ini tidak menggunakan controller tetapi langsung ditulis sebagai function di dalam `routes/web.php`

<br>
