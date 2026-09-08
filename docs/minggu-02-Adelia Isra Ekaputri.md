## Tugas 2.3 - READ

Nama: Adelia Isra Ekaputri  
NIM: 10241004

Ambil route `/tentang` yang telah di buat minggu lalu.  
1. Baris mana di `routes/web.php` yang menangkapnya?    
Jawaban: Route `/tentang` terletak pada baris 10 di file `routes/web.php`, yaitu pada `Route::get('/tentang', [CourseController::class, 'tentang']);`.

2. Kalau ditangani controller, berkas dan method mana?  
Jawaban: Route `/tentang` ditangani oleh Coursecontroller, yang berada pada file `app/Http/Controllers/Coursecontroller.php`, pada method `tentang()`.

3. View mana yang dikembalikan? Di path apa persisnya?  
Jawaban: View yang dikembalikan adalah `/tentang`, yang terdapat pada path `resources/views/tentang.blade.php`.

4. Layout apa yang membungkusnya?   
Jawaban: Layout yang membungkusnya adalah `Blade Component <x-layout>`. Berkas layout ini berapa pada `resources/views/components/layout.blade.php`, dan digunakan pada `tentang.blade.php` dengan `<x-layout title="...">...</x-layout>`.

5. Jalankan `php artisan route:list --path=tentang`. Cocok dengan analisis anda?    
Jawaban: <img src="Nomor 5.png">

Hasilnya cocok dengan analisis saya, karena route `/tentang` berada pada baris ke-10 pada `routes/web.php`, dan hasil `route:list` juga menunjukkan route tersebut.