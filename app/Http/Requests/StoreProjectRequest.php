<?php

namespace App\Http\Requests;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Rules\DueDateAfterStartDate;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_name' => ['required', 'string', 'max:255'],
            'project_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', new \Illuminate\Validation\Rules\Enum(ProjectStatus::class)],
            'priority' => ['required', new \Illuminate\Validation\Rules\Enum(ProjectPriority::class)],
            'start_date' => ['required', 'date'],
            'due_date' => ['required', 'date', new DueDateAfterStartDate($this->input('start_date'))],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'The status field is required.',
            'priority.required' => 'The priority field is required.',
        ];
    }
}