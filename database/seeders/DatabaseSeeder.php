<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Kata sandi demo: default "password" untuk lokal/CI.
        // WAJIB diganti di produksi lewat env DEMO_PASSWORD saat deployment.
        $demoPassword = env('DEMO_PASSWORD', 'password');

        // --- 1 admin ---
        $admin = User::factory()->admin()->create([
            'name' => 'Admin KampusLMS',
            'email' => 'admin@kampuslms.test',
            'nim_nip' => '198001012005011001', // NIP (18 digit)
            'password' => $demoPassword,
        ]);

        // --- 3 dosen ---
        $dosens = User::factory()->dosen()->count(2)->create();
        $dosenDemo = User::factory()->dosen()->create([
            'name' => 'Dosen Demo',
            'email' => 'dosen@kampuslms.test',
            'nim_nip' => '198505052010011002', // NIP (18 digit)
            'password' => $demoPassword,
        ]);
        $dosens->push($dosenDemo);

        // --- 30 mahasiswa ---
        $mahasiswas = User::factory()->mahasiswa()->count(29)->create();
        $mahasiswaDemo = User::factory()->mahasiswa()->create([
            'name' => 'Mahasiswa Demo',
            'email' => 'mahasiswa@kampuslms.test',
            'nim_nip' => '10241999', // NIM
            'password' => $demoPassword,
        ]);
        $mahasiswas->push($mahasiswaDemo);

        // --- 5 mata kuliah, tiap MK ≥15 mahasiswa terdaftar ---
        $courses = collect();
        for ($i = 0; $i < 5; $i++) {
            $course = Course::factory()->create([
                'lecturer_id' => $dosens->random()->id,
            ]);
            $courses->push($course);

            // enroll 20 mahasiswa acak (≥15) ke MK ini
            $enrolled = $mahasiswas->random(min(20, $mahasiswas->count()));
            foreach ($enrolled as $mhs) {
                $course->students()->attach($mhs->id, [
                    'enrolled_at' => now()->subDays(rand(1, 60)),
                ]);
            }
        }

        // --- 3 tugas per MK: campuran lewat deadline, aktif, draft ---
        foreach ($courses as $course) {
            $assignments = collect([
                Assignment::factory()->past()->create([
                    'course_id' => $course->id,
                    'created_by' => $course->lecturer_id,
                ]),
                Assignment::factory()->create([
                    'course_id' => $course->id,
                    'created_by' => $course->lecturer_id,
                ]),
                Assignment::factory()->draft()->create([
                    'course_id' => $course->id,
                    'created_by' => $course->lecturer_id,
                ]),
            ]);

            $enrolledStudents = $course->students;

            foreach ($assignments as $assignment) {
                if ($assignment->status !== 'published') {
                    continue; // tugas draft tidak menerima submission
                }

                // ~70% mahasiswa terdaftar mengumpulkan
                $submitters = $enrolledStudents->random(
                    max(1, (int) ($enrolledStudents->count() * 0.7))
                );

                foreach ($submitters as $student) {
                    $isLate = $assignment->due_at < now() && fake()->boolean(30);

                    $submittedAt = $isLate
                        ? Carbon::parse($assignment->due_at)->addHours(rand(1, 72))
                        : Carbon::parse($assignment->due_at)->subHours(rand(1, 72));

                    $submission = Submission::factory()->create([
                        'assignment_id' => $assignment->id,
                        'user_id' => $student->id,
                        'submitted_at' => $submittedAt,
                        'is_late' => $isLate,
                    ]);

                    // ~60% submission sudah dinilai
                    if (fake()->boolean(60)) {
                        Grade::factory()->create([
                            'submission_id' => $submission->id,
                            'graded_by' => $assignment->created_by,
                        ]);
                    }
                }
            }
        }
    }
}