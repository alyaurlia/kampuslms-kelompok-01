<?php

namespace App\Http\Requests\Api;

use App\Models\Grade;
use App\Models\Submission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class GradeSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $submission = $this->route('submission');
        $grade = $submission->grade;

        return $grade
            ? Gate::forUser($this->user())->allows('update', $grade)
            : Gate::forUser($this->user())->allows('create', [Grade::class, $submission]);
    }

    public function rules(): array
    {
        $submission = $this->route('submission');
        $maxScore = $submission instanceof Submission
            ? $submission->assignment->max_score
            : 100;

        return [
            'score' => ['required', 'numeric', 'min:0', 'max:' . $maxScore],
            'feedback' => ['nullable', 'string', 'max:2000'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'score.required' => 'Nilai wajib diisi.',
            'score.numeric'  => 'Nilai harus berupa angka.',
            'score.min'      => 'Nilai tidak boleh kurang dari 0.',
            'score.max'      => 'Nilai tidak boleh melebihi nilai maksimum tugas.',
            'feedback.string'=> 'Feedback harus berupa teks.',
            'feedback.max'   => 'Feedback maksimal 2000 karakter.',
            'is_published.boolean' => 'Status tampil nilai harus berupa true atau false.',
        ];
    }
}