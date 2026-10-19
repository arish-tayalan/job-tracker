<?php

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for both creating and editing an application.
 * If validation fails, Laravel redirects back with errors and old input automatically.
 */
class JobApplicationRequest extends FormRequest
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
            'company' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(ApplicationStatus::class)],
            'location' => ['nullable', 'string', 'max:255'],
            'job_url' => ['nullable', 'url:http,https', 'max:2048'],
            'applied_on' => ['nullable', 'date', 'before_or_equal:today'],
            'follow_up_on' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
