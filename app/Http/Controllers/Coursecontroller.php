<?php
 
namespace App\Http\Controllers;
 
class CourseController extends Controller
{
    /**
     * Sumber data statis sementara, menggantikan query ke database.
     * Ditulis sebagai method terpisah (bukan property class) supaya nanti
     * gampang diganti jadi Course::all() / Course::query() tanpa mengubah
     * signature method index() & show().
     */
    private function data(): array
    {
        return [
            [
                'id' => 1,
                'kode' => 'IF101',
                'nama' => 'Algoritma dan Pemrograman',
                'sks' => 3,
                'dosen' => 'Dr. Siti Aminah',
                'deskripsi' => 'Pengantar dasar algoritma, struktur kontrol, dan pemrograman prosedural.',
            ],
            [
                'id' => 2,
                'kode' => 'IF201',
                'nama' => 'Struktur Data',
                'sks' => 3,
                'dosen' => 'Budi Santoso, M.Kom.',
                'deskripsi' => 'Konsep dan implementasi struktur data seperti list, stack, queue, dan tree.',
            ],
            [
                'id' => 3,
                'kode' => 'IF301',
                'nama' => 'Basis Data',
                'sks' => 4,
                'dosen' => 'Dr. Andi Wijaya',
                'deskripsi' => 'Perancangan basis data relasional, normalisasi, dan SQL.',
            ],
        ];
    }
 
    /**
     * GET /mata-kuliah
     * Menampilkan daftar seluruh mata kuliah.
     * Belum ada filter per-role di sini karena datanya masih statis;
     * nanti saat sudah pakai database, filter "milik dosen" / "yang
     * tersedia untuk mahasiswa" diterapkan di query, bukan di view.
     */
    public function index()
    {
        $mataKuliah = $this->data();
 
        return view('mata-kuliah.index', compact('mataKuliah'));
    }
 
    /**
     * GET /mata-kuliah/{mata_kuliah}
     * Menampilkan detail satu mata kuliah berdasarkan id.
     * Parameter masih berupa id mentah (bukan route model binding) karena
     * belum ada model/Eloquent; nanti tinggal diganti jadi Course $mataKuliah
     * begitu modelnya dibuat.
     *
     * @param  int  $id
     */
    public function show(int $id)
    {
        // collect() dipakai supaya bisa pakai method firstWhere() yang ringkas,
        // menggantikan sementara apa yang nanti dilakukan Course::findOrFail().
        $mataKuliah = collect($this->data())->firstWhere('id', $id);
 
        // abort(404) dipanggil manual karena belum ada findOrFail() dari Eloquent;
        // ini menjaga perilaku tetap sama persis begitu nanti pindah ke database.
        abort_if(is_null($mataKuliah), 404);
 
        return view('mata-kuliah.show', compact('mataKuliah'));
    }

    public function tentang()
    {
        return view('tentang');
    }

}