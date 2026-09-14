# Minggu 3 - BREAK

Adelia Cyntia Renata NIM 10241003

1. Soal

    Hapus unique(['course_id','user_id']) dari course_user, lalu daftarkan mahasiswa yang sama dua kali

    Jawaban

    Dengan menghilangkan baris `$table->unique(['course_id', 'user_id'])` dari file migrasi `create_course_user_table` lalu menjalankan perintah `php artisan migrate:fresh --seed` agar perubahan tersebut diterapkan. Setelah itu saya mencoba mendaftarkan mahasiswa yang sama dengan ID 5 ke mata kuliah yang sama dengan ID 1 sebanyak dua kali secara manual melalui tinker menggunakan perintah `attach()`.

    Hasil dari eksekusi query `DB::table('course_user')->where('course_id', 1)->where('user_id', 5)->get()` menunjukkan ada 3 baris data yang memiliki pasangan course_id dan user_id yang sama.

    Baris id: 1 hasil pendaftaran otomatis dari seeder.
    Baris ID: 101 — hasil percobaan attach() yang pertama.
    Baris id: 102 — hasil percobaan attach() yang kedua.

<img src="docs/break3.1.jpeg">

    Tanpa unique, database tidak bisa menghindari data yang sama — validasi di aplikasi saja tidak cukup karena data bisa dimasukkan langsung ke database. Ketentuan ini penting untuk memastikan data pendaftaran tetap tepat dan akurat.

2. Soal

    Tambahkan role ke $fillable model User, lalu kirim request pembuatan user dengan role=admin lewat form yang tidak punya field role

    Jawaban

    Dengan menambahkan kolom `role` ke dalam `$fillable` di model `User` agar meniru situasi di mana perlindungan terhadap penugasan properti secara massal tidak berjalan dengan benar. Setelah itu saya membuat pengguna baru dengan data yang tidak sesuai menggunakan tinker melalui perintah `User::create()` dengan menambahkan `role => admin`, seolah-olah ini adalah permintaan dari form registrasi umum yang seharusnya hanya meminta `name`, `email`, dan `password`.

```php
$data = ['name' => 'Test User', 'email' => 'test123@test.com', 'password' => 'password', 'role' => 'admin'];
$u = App\Models\User::create($data);
$u->role;
```

<img src="docs/break3.2.jpeg">

3. Soal

    Ganti seluruh $fillable dengan protected $guarded = []; lalu ulangi nomor 2

    Jawaban

    Dengan mengganti seluruh isi `$fillable` pada model `User` dengan `protected $guarded = [];` agar meniru situasi di mana tidak ada kolom yang terlindungi dari pengisian massal. Selanjutnya, saya membuat pengguna baru dengan data lengkap menggunakan tinker dengan perintah `User::create()`, termasuk kolom `role => admin` dan `id => 999`, yang sebenarnya tidak bisa ditentukan secara manual oleh pengguna.

```php
$data = [
    'name' => 'Test Guarded',
    'email' => 'guarded@test.com',
    'password' => 'password',
    'role' => 'admin',
    'id' => 999,
];
$u = App\Models\User::create($data);
$u->id;
$u->role;
```

<img src="docs/break3.3.jpeg">

4. Soal

    Kosongkan isi down() di satu migrasi, lalu jalankan php artisan migrate:refresh

    Jawaban

    Dalam migrasi `create_courses_table`, metode `down()` sengaja dibiarkan kosong untuk menguji apakah migrasi dapat dibatalkan dengan benar.

Perubahan yang dilakukan:

```php
public function down(): void
{
}
```

Kemudian dijalankan perintah:

```bash
php artisan migrate:refresh
```

    Hasilnya, proses rollback migrasi `courses` terlihat berhasil, tetapi tabel `courses` sebenarnya tidak dihapus karena metode `down()` tidak memiliki perintah untuk menghapus tabel tersebut. Saat Laravel melanjutkan proses rollback untuk migrasi `users`, muncul kesalahan:

```text
SQLSTATE[HY000]: General error: 3730
Cannot drop table 'users' referenced by a foreign key constraint
'courses_lecturer_id_foreign' on table 'courses'.
```

    Ini terjadi karena tabel `courses` masih ada dan memiliki foreign key `lecturer_id` yang menghubungkan ke tabel `users`. Akibatnya, MySQL tidak memperbolehkan tabel `users` dihapus karena masih digunakan sebagai acuan oleh tabel `courses`.

    Bisa disimpulkan bahwa metode `down()` pada migrasi harus benar-benar membalikkan perubahan yang dilakukan oleh metode `up()`. Migrasi yang tidak bisa dibalikkan dapat menyebabkan perintah `migrate:refresh` tidak berhasil.

5. Soal

    Ubah restrictOnDelete pada lecturer_id menjadi cascadeOnDelete, lalu hapus satu dosen

    Jawaban

    Jadi kerusakan yang dilakukan ada di migration `create_courses_table`, `lecturer_id` yang seharusnya menggunakan `restrictOnDelete()` diubah menjadi:

```php
$table->foreignId('lecturer_id')
    ->constrained('users')
    ->cascadeOnDelete();
```

    Kemudian database di-reset menggunakan

```bash
php artisan migrate:fresh --seed
```

    Setelah itu dipilih seorang dosen yang masih memiliki mata kuliah. Ketika dosen tersebut dihapus melalui Tinker

```php
App\Models\User::find(ID_DOSEN)->delete();
```

    Jadi mata kuliah yang memiliki `lecturer_id` tersebut juga ikut terhapus.

    Hasil pengamatannya adalah penggunaan `cascadeOnDelete()` membuat penghapusan data induk (users) secara otomatis menghapus juga data anak (courses) yang memiliki foreign key terhubung ke dosen tersebut. Ini menunjukkan adanya hilangnya data secara berturut-turut.