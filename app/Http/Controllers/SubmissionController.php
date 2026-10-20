<?php

namespace App\Http\Controllers;

use App\Actions\SubmitAssignment;
use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\SubmissionRevision;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    private const DISK = 'local'; // storage/app/private

    /**
     * POST /mahasiswa/tugas/{assignment}/kumpul
     * Otorisasi + validasi di StoreSubmissionRequest, penyimpanan di
     * SubmitAssignment. Kumpul ulang menimpa pengumpulan lama.
     */
    public function store(StoreSubmissionRequest $request, Assignment $assignment, SubmitAssignment $submit)
    {
        $submit->handle(
            $assignment,
            $request->user(),
            $request->file('file'),
            $request->input('note'),
        );

        return back()->with('success', 'Tugas berhasil dikumpulkan.');
    }

    /**
     * GET /pengumpulan/{submission}/unduh
     * Pemilik, dosen pengampu, dan admin (SubmissionPolicy::view).
     */
    public function download(Submission $submission)
    {
        Gate::authorize('view', $submission);

        $disk = Storage::disk(self::DISK);

        abort_unless($disk->exists($submission->file_path), 404, 'Berkas tidak ditemukan di server.');

        // download() = selalu sebagai lampiran; nosniff mencegah browser
        // menebak ulang tipe berkas.
        return $disk->download(
            $submission->file_path,
            $submission->original_name,
            ['X-Content-Type-Options' => 'nosniff']
        );
    }

    /**
     * GET /pengumpulan/{submission}/revisi/{revision}/unduh
     * Berkas versi lama. Aturan akses sama dengan versi terbaru:
     * pemilik, dosen pengampu, dan admin (SubmissionPolicy::view).
     */
    public function downloadRevision(Submission $submission, SubmissionRevision $revision)
    {
        Gate::authorize('view', $submission);

        // Cegah akses silang: revisi harus milik submission yang diotorisasi.
        // Tanpa ini, pemilik submission A bisa meminta revisi submission B.
        abort_unless($revision->submission_id === $submission->id, 404);

        $disk = Storage::disk(self::DISK);

        abort_unless($disk->exists($revision->file_path), 404, 'Berkas tidak ditemukan di server.');

        return $disk->download(
            $revision->file_path,
            $revision->original_name,
            ['X-Content-Type-Options' => 'nosniff']
        );
    }
}