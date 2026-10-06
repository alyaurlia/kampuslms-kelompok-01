<?php

namespace App\Http\Requests\Api;

use App\Models\Submission;
use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $submission = $this->route('submission');
        $maxScore = $submission instanceof Submission
            ? $submission->assignment->max_score
            : 100;

        return [
            'score' => ['required', 'numeric', 'min:0', 'max:' . $maxScore],
            'feedback' => ['nullable', 'string'],
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
        ];
    }
}
