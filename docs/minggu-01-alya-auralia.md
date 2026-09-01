# Catatan Minggu 01
## Nama: Alya Auralia
## NIM: 10241008
## Mata Kuliah: Pemrograman Web B

# Read
### 1. Buka `public/index.php.` Baca dari atas ke bawah. Tulis dalam 3 kalimat apa yang dilakukan berkas ini. <Br>
Jawaban=
1. Berkas `public/index.php` merupakan pintu masuk utama setiap kali ada orang membuka aplikasi ini, jadi aplikasi Laravel lah yang pertama kali dijalankan ketika sebuah aplikasi menerima permintaan dari pengguna. 
2. Berkas ini akan mencatat jam berapa (dalam microdetik) request ini mulai diproses, memeriksa apakah aplikasi sedang berada dalam mode maintenance, memuat semua library melalui Composer autoloader, dan melakukan bootstrap (menyalakan) aplikasi Laravel melalui berkas `bootstrap/app.php`.
3. Setelah aplikasi siap, Laravel menangkap request yang masuk menggunakan `Request::capture()` dan menyerahkannya melalui `$app->handleRequest()` ke aplikasi untuk diproses dan dikembalikan sebagai response.

### 2. Buka `bootstrap/app.php.` Identifikasi bagian mana yang mengurus route, mana yang mengurus middleware, mana yang mengurus exception.
Jawaban=<Br>
File `bootstrap/app.php`. Fungsinya adalah mengatur konfigurasi dasar aplikasi Laravel, semacam "pengaturan awal" sebelum aplikasi benar-benar jalan. Berikut bagian-bagiannya:
1. Bagian Routing:<Br>
```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```
Bagian ini mengatur route, yaitu memberi tahu Laravel, "halaman/URL apa saja yang tersedia, dan file mana yang mengatur alamat-alamat tersebut". Khususnya:<Br>
- `web`: menunjuk ke file `routes/web.php`. untuk mendefinisikan route atau URL ke halaman web aplikasi. Di sini biasanya ada daftar alamat-alamat website (misalnya /home, /tentang-kami, dll).
- `commands`: → menunjuk ke `routes/console.php`, ini untuk perintah-perintah yang dijalankan lewat terminal (bukan lewat browser).
- `health`: → `health: '/up'` menyediakan endpoint `/up` yang berfungsi sebagai "cek kesehatan" aplikasi (untuk tahu apakah server masih hidup/berjalan normal).
2. Bagian Middldeware:
```php
->withMiddleware(function (Middleware $middleware) {
    //
})
```
Ini bagian yang mengurus middleware, yaitu seperti "penjaga pintu" atau "filter" yang memeriksa setiap request sebelum sampai ke tujuan akhirnya. Middleware bisa dipakai misalnya untuk: mengecek apakah user sudah login, mencatat log aktivitas, dll. Jadi, yaitu lapisan yang dapat memeriksa atau memproses request sebelum request tersebut diteruskan ke route atau bagian aplikasi lainnya.
3. Bagian Exception
```php
->withExceptions(function (Exceptions $exceptions) {
    //
})
```
Ini bagian yang mengurus penanganan error/kesalahan (exception), misalnya kalau terjadi error di aplikasi, bagian ini yang menentukan bagaimana cara menangani, menampilkan atau mencatat error tersebut.

### 3. Buka `routes/web.php`. Temukan route yang menghasilkan halaman selamat datang. Ubah teksnya, muat ulang browser, pastikan berubah.<Br>
Jawaban=<Br>
Pada `routes/web.php` yang menghasilkan halaman selamat datang ada pada:
```php
Route::get('/', function () {
    return view('welcome');
});
```
Route ini memanggil view welcome, yang isinya terletak di file `resources/views/welcome.blade.php.` Sehingga untuk mengubah teks pada halamannya yaitu:
- Membuka file `resources/views/welcome.blade.php` dan mengubah teks judul dan deskripsi sesuai keinginan pada bagian kode:
```php
 <h1 class="mb-1 font-medium">Let's get started</h1>
   <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">Laravel has an incredibly rich ecosystem. <br>We suggest starting with the following.</p>
```
Setelah mengubah teksnya, kita buka `http://127.0.0.1:8000` di browser (server dijalankan dengan php artisan serve), lalu memuat ulang (refresh) halaman. Maka perbandingan hasil sebelum dan sesudahnya akan keluar menjadi seperti ini:<Br>
- Sebelum diubah
![before](assets/Before.png)
- Sesudah diubah
![after](assets/After.png)

### 4. Jalankan `php artisan route:list`. Cocokkan keluarannya dengan isi routes/web.php.<Br>
Jawaban=<Br>
- Perintah `php artisan route:list` digunakan untuk melihat seluruh route yang terdaftar pada aplikasi Laravel. Setelah menjalankan perintah tersebut, dapat di cocokan dengan `routes/web.php`yaitu: 

![Hasil](assets/Hasil.png)

- Terdapat route dengan method `GET|HEAD` dan URI `/`, yang sesuai dengan kode `Route::get('/', function () { return view('welcome'); });` pada file `routes/web.php`. 
- Tiga route lain (`storage/{path}` GET, `storage/{path}` PUT, dan `up`) bukan berasal dari `routes/web.php`, melainkan route bawaan Laravel:
    - `storage/{path}` GET dan `storage/{path}` PUT: digunakan untuk mengakses dan mengunggah file melalui sistem `storage/filesystem Laravel`.
    - `up`: route health check yang didaftarkan lewat parameter `health: '/up'` di `bootstrap/app.php`.

Jadi, dari 4 route yang terdaftar, hanya route `GET|HEAD /` yang berasal dari `routes/web.php`, sedangkan 3 route lainnya merupakan route bawaan (default) framework Laravel

# Break
### Lakukan satu per satu, catat pesan errornya, lalu kembalikan:

| # | Yang dirusak | Prediksi Anda sebelum mencoba | Pesan error sebenarnya |
|---|--------------|-------------------------------|------------------------|
| 1 | Ganti nama `.env` menjadi `.env.bak` | aplikasi Laravel akan mengalami masalah karena file .env berisi konfigurasi penting aplikasi. Tanpa file tersebut, Laravel tidak dapat menjalankan beberapa bagian aplikasi dengan benar karena Laravel tidak lagi menemukan file .env. |500 Server Error |
| 2 | Kosongkan nilai `APP_KEY` di `.env` | aplikasi Laravel akan menghasilkan error karena APP_KEY digunakan sebagai kunci penting. Jika nilainya kosong, Laravel kemungkinan tidak dapat menjalankan proses.|Internal Server Error.Illuminate\Encryption\MissingAppKeyException. No application encryption key has been specified. |
| 3 | Ubah `DB_DATABASE` menjadi nama yang tidak ada |Laravel akan gagal terhubung ke database karena nilai DB_DATABASE diarahkan ke database yang tidak ada. Aplikasi kemungkinan menampilkan pesan error yang menjelaskan bahwa database tidak ditemukan atau koneksi database gagal. |Illuminate\Database\QueryException |
| 4 | Ubah `APP_DEBUG=false`, lalu ulangi nomor 3 |Saya memprediksi error database tetap terjadi karena nama database masih tidak valid, tetapi ketika APP_DEBUG=false, informasi error yang ditampilkan kepada pengguna akan lebih umum dan tidak sedetail ketika mode debug aktif.|500 Server Error |