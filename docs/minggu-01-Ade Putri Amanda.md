Nama    : Ade Putri Amanda

NIM     : 10241002

# Read → Break → Fix → Build #

## READ — Bedah instalasi Anda sendiri (45 menit)

Setelah instalasi selesai dan halaman selamat datang Laravel muncul, kerjakan tanpa AI:
1. Buka `public/index.php`. Baca dari atas ke bawah. Tulis dalam 3 kalimat apa yang dilakukan berkas ini. 

Jawab: `public/index.php` adalah pintu masuk utama saat website Laravel dibuka. File ini menyiapkan dan menjalankan aplikasi Laravel. Setelah itu, request dari browser diproses oleh Laravel dan menghasilkan response.
   
2. Buka `bootstrap/app.php`. Identifikasi bagian mana yang mengurus route, mana yang mengurus middleware, mana yang mengurus exception.

Jawab: Bagian yang mengurus `route` (mengatur jalur/URL) adalah `withRouting()`, bagian `middleware` (mengatur pengecekan/perantara) adalah `withMiddleware()`, dan bagian `exception` (mengatur penanganan error) adalah `withExceptions()`. 

3. Buka `routes/web.php`. Temukan route yang menghasilkan halaman selamat datang. Ubah teksnya, muat ulang browser, pastikan berubah.

Jawab: 

`<?php`

`use Illuminate\Support\Facades\Route;`

`Route::get('/', function () {`
    `return view('welcome');`
`});`

<img src="asset/selamat datang.png">

`<?php`

`use Illuminate\Support\Facades\Route;`

`Route::get('/', function () {`
   ` return ('kelompok 1');`
`});`

<img src="asset/kelompok 1.png">

4. Jalankan `php artisan route:list`. Cocokkan keluarannya dengan isi `routes/web.php`.
Tulis jawabannya di *docs/minggu-01-catatan.md* di repo kelompok. Setiap anggota menulis catatan sendiri di berkas terpisah *(docs/minggu-01-<nama>.md)*.  

Jawab: 

<img src="asset/php artisan route list.png">

`route GET /` yang muncul di `route:list` berasal dari `Route::get('/')` pada `routes/web.php`. `Route` tersebut menjalankan `view('welcome')`, sehingga ketika alamat utama website dibuka, Laravel menampilkan halaman *welcome*. 