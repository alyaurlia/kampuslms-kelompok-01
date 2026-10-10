<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    /**
     * POST /mahasiswa/tugas/{assignment}/kumpul
     * Satu pengumpulan per tugas. Telat tetap diterima, ditandai is_late.
     */
    public function store(Request $request, Assignment $assignment)
    {
        // Mahasiswa terdaftar di MK tugas ini dan tugas published
        Gate::authorize('submit', $assignment);

        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'file.required' => 'Berkas tugas wajib diunggah.',
            'file.file'     => 'Berkas yang diunggah tidak valid.',
            'file.max'      => 'Ukuran berkas maksimal 10 MB.',
        ]);

        $user = $request->user();

        if ($assignment->submissions()->where('user_id', $user->id)->exists()) {
            return back()->withErrors(['file' => 'Anda sudah mengumpulkan tugas ini.']);
        }

        $file = $request->file('file');
        $submittedAt = now();

        // Disk 'local' = storage/app/private (bukan public)
        $assignment->submissions()->create([
            'user_id'       => $user->id,
            'file_path'     => $file->store("submissions/{$assignment->id}", 'local'),
            'original_name' => $file->getClientOriginalName(),
            'file_size'     => $file->getSize(),
            'note'          => $request->input('note'),
            'submitted_at'  => $submittedAt,
            'is_late'       => $assignment->due_at !== null && $submittedAt->gt($assignment->due_at),
        ]);

        return back()->with('success', 'Tugas berhasil dikumpulkan.');
    }

    /**
     * GET /pengumpulan/{submission}/unduh
     * Pemilik, dosen pengampu, dan admin (SubmissionPolicy::view).
     */
    public function download(Submission $submission)
    {
        Gate::authorize('view', $submission);

        return Storage::disk('local')->download(
            $submission->file_path,
            $submission->original_name
        );
    }
}