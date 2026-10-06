<?php

namespace App\Http\Requests;

use App\Models\Assignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('assignment'));
    }

    public function rules(): array
    {
        // PUT = semua field wajib; PATCH = parsial. course_id tidak bisa diubah.
        $required = $this->isMethod('PUT') ? 'required' : 'sometimes';

        return [
            'title' => [$required, 'string', 'max:255'],
            'instructions' => [$required, 'string'],
            'due_at' => [$required, 'date'],
            'max_score' => ['sometimes', 'integer', 'between:0,255'],
            'allow_late' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in(Assignment::STATUSES)],
        ];
    }
}