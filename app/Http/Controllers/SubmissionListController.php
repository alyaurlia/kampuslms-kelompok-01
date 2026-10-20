<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * GET /dosen/pengumpulan?tugas={id}&status={semua|belum|sudah|terlambat}&q={nama atau NIM}
 *
 * Daftar pengumpulan per tugas untuk dosen: siapa sudah, siapa belum,
 * dan siapa yang terlambat. Daftar dimulai dari MAHASISWA TERDAFTAR di
 * mata kuliah (bukan dari tabel submissions), karena mahasiswa yang belum
 * mengumpulkan tidak punya baris di submissions.
 */
class SubmissionListController extends Controller
{
    private const STATUSES = ['semua', 'belum', 'sudah', 'terlambat'];
    private const PER_PAGE = 20;

    public function index(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'tugas'  => ['nullable', 'integer'],
            'status' => ['nullable', Rule::in(self::STATUSES)],
            'q'      => ['nullable', 'string', 'max:100'],
        ]);
        $status = $data['status'] ?? 'semua';
        $q      = trim($data['q'] ?? '');

        // Pilihan tugas: hanya tugas published milik MK yang diampu dosen ini.
        // Course::scopeVisibleTo => dosen hanya melihat MK dengan lecturer_id = dirinya.
        // with('course') tanpa batasan kolom: policy butuh lecturer_id.
        $assignments = Assignment::query()
            ->whereHas('course', fn ($q) => $q->visibleTo($user))
            ->where('status', 'published')
            ->with('course')
            ->withCount('submissions')
            ->orderByDesc('due_at')
            ->get();

        $selected = null;
        $rows     = null;
        $stats    = null;

        if (! empty($data['tugas'])) {
            // Tugas milik dosen lain tidak ada di $assignments => 404, bukan 403,
            // supaya keberadaan tugas itu tidak terkonfirmasi.
            $selected = $assignments->firstWhere('id', (int) $data['tugas']);
            abort_if($selected === null, 404);

            // Lapisan kedua: tetap lewat Policy.
            Gate::authorize('viewSubmissions', $selected);

            $stats = $this->stats($selected);
            $rows  = $this->rows($selected, $status, $q);
        }

        return view('dosen.pengumpulan.index', [
            'assignments' => $assignments,
            'selected'    => $selected,
            'rows'        => $rows,
            'stats'       => $stats,
            'status'      => $status,
            'q'           => $q,
        ]);
    }

    /**
     * Ringkasan dalam SATU query: total terdaftar, sudah mengumpulkan, terlambat.
     * LEFT JOIN supaya mahasiswa tanpa submission tetap terhitung di total.
     */
    private function stats(Assignment $assignment): array
    {
        $row = DB::table('course_user as cu')
            ->leftJoin('submissions as s', function ($join) use ($assignment) {
                $join->on('s.user_id', '=', 'cu.user_id')
                     ->where('s.assignment_id', '=', $assignment->id);
            })
            ->where('cu.course_id', $assignment->course_id)
            ->selectRaw('COUNT(*) as total, COUNT(s.id) as submitted, COALESCE(SUM(s.is_late), 0) as late')
            ->first();

        $total     = (int) $row->total;
        $submitted = (int) $row->submitted;

        return [
            'total'     => $total,
            'submitted' => $submitted,
            'belum'     => $total - $submitted,
            'late'      => (int) $row->late,
        ];
    }

    /**
     * Baris daftar: satu per mahasiswa terdaftar, dengan submission-nya (bila ada).
     * Total query konstan (tidak N+1): hitung, daftar mahasiswa, lalu submission+grade.
     */
    private function rows(Assignment $assignment, string $status, string $q = '')
    {
        $sudahMengumpulkan = function ($q) use ($assignment, $status) {
            $q->select(DB::raw(1))
                ->from('submissions')
                ->whereColumn('submissions.user_id', 'users.id')
                ->where('submissions.assignment_id', $assignment->id);

            if ($status === 'terlambat') {
                $q->where('submissions.is_late', true);
            }
        };

        $students = $assignment->course->students()
            ->when($status === 'belum', fn ($q) => $q->whereNotExists($sudahMengumpulkan))
            ->when(in_array($status, ['sudah', 'terlambat'], true), fn ($q) => $q->whereExists($sudahMengumpulkan))
            // Pencarian nama / NIM. Dibungkus satu closure supaya OR tidak merusak
            // batasan course_user pada relasi. Karakter % dan _ di-escape.
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';

                $query->where(fn ($w) => $w
                    ->where('users.name', 'like', $like)
                    ->orWhere('users.nim_nip', 'like', $like));
            })
            ->orderBy('users.name')
            // Hanya kolom yang dipakai tampilan.
            ->paginate(self::PER_PAGE, ['users.id', 'users.name', 'users.nim_nip'])
            ->withQueryString();

        // Submission hanya untuk mahasiswa di halaman ini.
        $submissions = Submission::query()
            ->where('assignment_id', $assignment->id)
            ->whereIn('user_id', $students->getCollection()->pluck('id'))
            ->with('grade')
            ->get()
            ->keyBy('user_id');

        return $students->through(function ($student) use ($submissions) {
            $submission = $submissions->get($student->id);

            return (object) [
                'student'    => $student,
                'submission' => $submission,
                'status'     => match (true) {
                    $submission === null => 'belum',
                    $submission->is_late => 'terlambat',
                    default              => 'tepat_waktu',
                },
            ];
        });
    }
}