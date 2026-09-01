## Tugas 1.3 - READ

Nama: Adelia Isra Ekaputri  
NIM: 10241004

1. Buka public/index.php. Baca dari atas ke bawah. Tulis dalam 3 kalimat apa yang dilakukan berkas ini.     
Jawaban: Berkas ini berfungsi sebagai titik masuk utama pada Laravel yang memulihkan konstanta waktu start, dan memeriksa status mode pemeliharaan. Selanjutnya, berkas memuat autoloader composer untuk mengimpor semua pustaka yang dibutuhkan aplikasi secara otomatis. Terakhir, berkas memuat bootstrap aplikasi dari bootstrap/app.php dan menangkap HTTP request masuk untuk diproses sampai menghasilkan respons ke pengguna.

2. Buka bootstrap/app.php. Identifikasi bagian mana yang mengurus route, mana yang mengurus middleware, mana yang mengurus exception.   
Jawaban:    
- Bagian Routing: Bagian ini mendaftarkan berkas rute web `(routes/web.php)`, rute perintah konsol `(routes/console.php)`, serta rute health check `(/up)`.
```
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
```

- Bagian Middleware: Tempat ini digunakan untuk mendaftarkan atau mengonfigurasi middleware global maupun custom aplikasi.
```
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
```

- Bagian Exception: Bagian ini digunakan untuk mengatur penanganan kesalahan (error/exception handling) kustom aplikasi.
```
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

3. routes/web.php. Temukan route yang menghasilkan halaman selamat datang. Ubah teksnya, muat ulang browser, pastikan berubah.  
Jawaban: Tempat untuk mengubah teks pada halaman selamat datang ada pada `resources/views/welcome.blade.php`
<img src="code no 3.png">

Tampilan sebelum di ubah:
<img src="Sebelum diubah.png">

Tampilan setelah di ubah:
<img src="Setelah diubah.png">

4. Jalankan php artisan route:list. Cocokkan keluarannya dengan isi routes/web.php  
Jawaban:
<img src="no 4.png">
Berdasarkan hasil eksekusi perintah `php artisan route:list` dan isi berkas `routes/web.php`, terdapat keterkaitan langsung meskipun tidak semua rute berasal dari berkas `web.php`. Dari total 4 rute yang terdaftar pada sistem, hanya rute utama `GET|HEAD /` yang didefinisikan secara eksplisit di dalam `routes/web.php` pada baris ke-5 untuk menampilkan tampilan welcome. Sementara itu, tiga rute lainnya didaftarkan secara otomatis oleh kerangka kerja Laravel di luar berkas `web.php`. Rute `GET|HEAD up` berasal dari konfigurasi health check yang diatur pada `bootstrap/app.php`, sedangkan dua rute `storage/{path}` (metode `GET` dan `PUT`) dibuat secara internal oleh `FilesystemServiceProvider` bawaan Laravel untuk mengelola berkas media dan penyimpanan lokal.