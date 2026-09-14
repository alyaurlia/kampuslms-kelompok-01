Nama    : Ade Putri Amanda

NIM     : 10241002

# Read → Break → Fix → Build #

## BREAK — Lima kerusakan (45 menit)

| # | Yang dicoba | Yang harus Anda amati |
|---|-------------|-----------------------|
| 1 | Hapus `unique(['course_id','user_id'])` dari `course_user`, lalu daftarkan mahasiswa yang sama dua kali | Data ganda lolos tanpa keluhan |
| 2 | Tambahkan `role` ke `$fillable` model `User`, lalu kirim request pembuatan user dengan `role=admin` lewat form yang tidak punya field `role` | **Mass assignment nyata** — Anda baru saja jadi admin |
| 3 | Ganti seluruh `$fillable` dengan `protected $guarded = [];` lalu ulangi nomor 2 | Kenapa `$guarded` kosong dilarang |
| 4 | Kosongkan isi `down()` di satu migrasi, lalu jalankan `php artisan migrate:refresh` | Migrasi tidak reversible = CI merah |
| 5 | Ubah `restrictOnDelete` pada `lecturer_id` menjadi `cascadeOnDelete`, lalu hapus satu dosen | Kehilangan data berantai |

### Jawaban nomor 1 s.d 5:

1. Jawab: <img src="image/break1.jpeg">

Saya menghapus baris `$table->unique(['course_id', 'user_id'])` dari _migration_ `create_course_user_table`, kemudian menjalankan `php artisan migrate:fresh --seed` dengan tujuan untuk menerapkan perubahan. Lalu, perubahan tersebut diterapkan.

Setelah itu, saya mencoba mendaftarkan mahasiswa yang sama `(user_id = 5) ke mata kuliah yang sama `(course_id = 1)` sebanyak dua kali melalui Tinker menggunakan `attach()`.

Saya kemudian mengecek data menggunakan `query DB::table('course_user')->where('course_id', 1)->where('user_id', 5)->get()`. Hasilnya menunjukkan terdapat 3 baris dengan pasangan `course_id` dan `user_id` yang sama, yaitu:

id 1 → data pendaftaran dari seeder
id 101 → hasil attach() pertama
id 102 → hasil attach() kedua

Hasil ini menunjukkan bahwa setelah _unique_ dihapus, mahasiswa yang sama dapat terdaftar lebih dari satu kali pada mata kuliah yang sama. Jadi, `unique(['course_id', 'user_id'])` diperlukan agar database mencegah adanya data pendaftaran yang sama.

2. Jawab: Saya menambahkan kolom _role_ ke dalam `$fillable pada model User`. Setelah itu, saya mencoba membuat _user_ baru melalui Tinker menggunakan `User::create()` dengan memasukkan `role => admin`, seolah-olah data tersebut berasal dari form registrasi yang sebenarnya tidak memiliki _field role_.

$data = ['name' => 'Test User', 'email' => 'test123@test.com', 'password' => 'password', 'role' => 'admin'];

`$u = App\Models\User::create($data);`

$u->role;

Hasil eksekusi $u->role menunjukkan nilai "admin".

<img src="image/break2.jpeg">

Hasil tersebut menunjukkan bahwa ketika _role_ dimasukkan ke dalam `$fillable`, kolom tersebut dapat diisi melalui _mass assignment_. Padahal, _form_ registrasi tidak menyediakan pilihan _role_ dan _user_ baru seharusnya mendapatkan _role_ default mahasiswa. Hal ini menunjukkan bahwa memasukkan _role_ ke dalam `$fillable` dapat menyebabkan _user_ mengisi _role_ yang seharusnya tidak boleh ditentukan sendiri.

3. Jawab: Saya mengganti `$fillable pada model User dengan protected $guarded = [];`. Artinya, tidak ada kolom yang dilindungi dari mass assignment, sehingga semua kolom dapat diisi menggunakan `User::create()`.

Setelah itu, saya mencoba membuat _user_ baru melalui Tinker dengan mengisi beberapa data, termasuk `role => admin dan id => 999`. Hasilnya menunjukkan bahwa data role berhasil diisi menjadi admin dan id berhasil menggunakan nilai 999.

<img src="image/break3.jpeg">

Hal ini menunjukkan bahwa penggunaan `protected $guarded = [];` membuat semua kolom dapat diisi melalui _mass assignment_, termasuk kolom yang seharusnya tidak boleh ditentukan langsung oleh pengguna seperti role dan id. Karena itu, penggunaan `$guarded = []` berisiko membuat pengguna dapat mengubah data yang seharusnya dilindungi.

4. Jawab: Pada `migration create_courses_table`, saya  mengosongkan isi method `down()` untuk melihat apa yang terjadi saat migration di-rollback.

Perubahan yang dilakukan:

• `public function down(): void
{
}`

 Kemudian saya menjalankan perintah:

` php artisan migrate:refresh `

Memiliki perintah untuk menghapus tabel tersebut. Saat Laravel melanjutkan proses _rollback_ ke tabel _users_, muncul error karena tabel _courses_ masih ada dan masih memiliki `foreign key lecturer_id` yang mengarah ke tabel _users_. Hal ini menyebabkan database tidak mengizinkan tabel +users_ dihapus karena masih digunakan oleh tabel _courses_.

5. Jawab: Pada `migration create_courses_table`, saya mengubah `restrictOnDelete()` menjadi `cascadeOnDelete()`:

$table->foreignId('lecturer_id')
    ->constrained('users')
    ->cascadeOnDelete();

Kemudian database di-reset menggunakan `php artisan migrate:fresh --seed`. Setelah itu, saya menghapus seorang dosen yang masih memiliki mata kuliah melalui Tinker. Nah, mata kuliah yang memiliki `lecturer_id` dosen tersebut ikut terhapus secara otomatis. Hal ini menunjukkan bahwa `cascadeOnDelete()` dapat menyebabkan data mata kuliah ikut terhapus ketika data dosen dihapus, sehingga berisiko menyebabkan kehilangan data secara berantai.