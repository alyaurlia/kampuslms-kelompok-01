<?php

namespace App\Http\Requests;

use App\Models\Assignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssignmentRequest extends FormRequest
{
    // Otorisasi dilakukan di controller lewat Gate::authorize().
    public function authorize(): bool
    {
        return true;
    }

    // Checkbox tidak terkirim kalau tidak dicentang, jadi paksa jadi boolean.
    protected function prepareForValidation(): void
    {
        $this->merge(['allow_late' => $this->boolean('allow_late')]);
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string'],
            'due_at'       => ['required', 'date', 'after:now'],
            'max_score'    => ['required', 'integer', 'min:1', 'max:100'],
            'allow_late'   => ['boolean'],
            'status'       => ['required', Rule::in(Assignment::STATUSES)],
            'attachment'   => [
                'nullable', 'file',
                'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,txt,jpg,jpeg,png',
                'max:10240', // 10 MB
            ],
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