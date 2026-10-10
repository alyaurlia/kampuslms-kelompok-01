<?php

namespace App\Http\Requests;

use App\Models\Assignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'allow_late'        => $this->boolean('allow_late'),
            'remove_attachment' => $this->boolean('remove_attachment'),
        ]);
    }

    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:255'],
            'instructions'      => ['required', 'string'],
            // Saat edit, batas waktu boleh yang sudah lewat.
            'due_at'            => ['required', 'date'],
            'max_score'         => ['required', 'integer', 'min:1', 'max:100'],
            'allow_late'        => ['boolean'],
            'status'            => ['required', Rule::in(Assignment::STATUSES)],
            'attachment'        => [
                'nullable', 'file',
                'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,txt,jpg,jpeg,png',
                'max:10240',
            ],
            'remove_attachment' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title'        => 'judul',
            'instructions' => 'instruksi',
            'due_at'       => 'batas waktu',
            'max_score'    => 'nilai maksimal',
            'status'       => 'status',
            'attachment'   => 'lampiran',
        ];
    }
}