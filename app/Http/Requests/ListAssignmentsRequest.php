<?php

namespace App\Http\Requests;

use App\Models\Assignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ListAssignmentsRequest extends FormRequest
{
    // Otorisasi dulu (403) sebelum validasi (422).
    public function authorize(): bool
    {
        return Gate::allows('view', $this->route('course'));
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', Rule::in(Assignment::STATUSES)],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ];
    }
}