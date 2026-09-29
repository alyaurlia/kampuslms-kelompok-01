Nama    : Ade Putri Amanda

NIM     : 10241002

# Read → Break → Fix → Build #

## READ — Telusuri satu siklus form gagal (30 menit)

Tanpa AI. Buat _form_ tambah mata kuliah, isi dengan data yang pasti tidak valid (SKS = 99), kirim, lalu jawab:

1. Method apa yang menerima _request_? Di _controller_ mana?

Jawab: Method yang menerima request adalah `store()` pada `CourseController`, yang berada di `app/Http/Controllers/CourseController.php`. Method ini menerima data dari _form_ tambah mata kuliah yang dikirim menggunakan method `POST` ke route `/mata-kuliah`, dengan _request_ yang diterima melalui parameter `StoreCourseRequest $request`.

2. Di titik mana persisnya validasi terjadi - sebelum atau sesudah baris pertama method _controller_?

Jawab: Validasi terjadi *sebelum baris pertama* isi method `store()` dijalankan. Karena `store()` menggunakan parameter `StoreCourseRequest $request`, kemudian Laravel akan menjalankan aturan validasi dari `StoreCourseRequest` terlebih dahulu. Jika data tidak valid, misalnya *SKS = 99*, maka kode di dalam `store()` tidak dijalankan dan Laravel langsung mengembalikan pengguna ke halaman _form_.

3. Ke mana Laravel me-_redirect_ setelah gagal? Siapa yang menentukan tujuannya?

Jawab: Setelah validasi gagal, Laravel akan mengembalikan pengguna ke halaman asal _form_, yaitu `/mata-kuliah/create`. Tujuan _redirect_ ini ditentukan oleh mekanisme bawaan Laravel pada `FormRequest`, bukan oleh kode _redirect_ yang ditulis di dalam method `store()`. Hal ini berbeda dengan _redirect_ ketika data berhasil disimpan, yaitu `redirect()->route('mata-kuliah.index')`, yang memang ditulis secara langsung di dalam method `store()`.

4. Dari mana `@error('sks')` mengambil pesannya?

jawab: `@error('sks')` mengambil pesan kesalahan dari `$errors`, yaitu kumpulan _error_ validasi yang otomatis tersedia di _view_ setelah validasi gagal. Pesan _error_ tersebut berasal dari aturan validasi pada `StoreCourseRequest`. Jika di `messages()` terdapat pesan khusus seperti `'sks.between' => 'SKS harus antara 1 sampai 6.'`, maka pesan itulah yang ditampilkan oleh `@error('sks')`.

5. Dari mana `old('sks')` mengambil nilainya? Berapa lama nilai itu bertahan?

Jawab: `old('sks')` mengambil nilai dari _session_. Saat validasi gagal, Laravel menyimpan sementara nilai yang tadi dimasukkan, misalnya *99*. Jadi ketika kembali ke _form_, nilai *99* bisa muncul lagi. Nilai tersebut hanya disimpan sementara untuk _request_ berikutnya, namun setelah itu tidak digunakan lagi.

6. Buka _DevTools → Application → Cookies_. Temukan _cookie session_ Laravel. Catat namanya.

Jawab: Nama _cookie session_ Laravel yang ditemukan adalah *laravel_session*. Nilai `old()` sendiri hanya bertahan sementara, yaitu saat halaman yang menggunakan `old()` ditampilkan setelah validasi gagal. Setelah digunakan pada _request_ berikutnya, maka nilai tersebut tidak disimpan lagi.

---------------------------------------------------------------------------------------------------

## BREAK — Tujuh kerusakan (45 menit)

| # | Yang dirusak | Yang harus Anda amati |
|---|---|---|
| 1 | Hapus `@csrf` dari form, lalu kirim | Error 419 — dan renungkan apa yang dicegahnya |
| 2 | Ganti `$request->validated()` menjadi `$request->all()`, lalu kirim field liar lewat `curl` | Mass assignment kembali terbuka |
| 3 | Hapus validasi `exists:users,id` pada `lecturer_id`, kirim `lecturer_id=99999` | Data yatim masuk database |
| 4 | Hapus validasi `in:...` pada `status`, kirim `status=superadmin` | Enum jebol |
| 5 | Hapus `->withQueryString()`, lakukan pencarian lalu klik halaman 2 | Filter hilang — bug klasik |
| 6 | Ganti `return redirect()` menjadi `return view()` pada `store`, lalu tekan F5 setelah simpan | Data ganda; ini alasan PRG ada |
| 7 | Hapus `old(...)` dari semua input, lalu kirim form dengan satu kesalahan | Rasakan sendiri sebagai pengguna |

Nomor 2 dan 3 wajib dicoba lewat curl, bukan lewat browser:

curl -X POST http://kampuslms.test/courses \
  -H "X-CSRF-TOKEN: <ambil dari halaman>" \
  -b cookies.txt \
  -d "code=XX01" -d "name=Uji" -d "sks=3" \
  -d "lecturer_id=99999" -d "status=superadmin"
Catat hasilnya di docs/minggu-04-<nama>.md.