<?php

namespace App\Http\Requests;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $id = filter_var($this->input('course_id'), FILTER_VALIDATE_INT);
        $course = $id === false ? null : Course::find($id);

        return Gate::allows('create', [Assignment::class, $course]);
    }

    public function rules(): array
    {
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string'],
            'due_at' => ['required', 'date'],
            'max_score' => ['sometimes', 'integer', 'between:0,255'],
            'allow_late' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in(Assignment::STATUSES)],
        ];
    }
}