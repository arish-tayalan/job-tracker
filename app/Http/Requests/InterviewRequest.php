<?php

namespace App\Http\Requests;

use App\Enums\InterviewType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ownership is checked in the controller via the policy.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Past dates are allowed so you can log interviews that already happened.
            'scheduled_at' => ['required', 'date'],
            'type' => ['required', Rule::enum(InterviewType::class)],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
