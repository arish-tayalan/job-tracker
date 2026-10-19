{{-- Shared fields for create + edit. old() refills the form after a validation error. --}}
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
    <div>
        <x-input-label for="company" value="Company" />
        <x-text-input id="company" name="company" type="text" class="mt-1 block w-full"
            :value="old('company', $application->company)" required autofocus />
        <x-input-error :messages="$errors->get('company')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="position" value="Position" />
        <x-text-input id="position" name="position" type="text" class="mt-1 block w-full"
            :value="old('position', $application->position)" required />
        <x-input-error :messages="$errors->get('position')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="status" value="Status" />
        @php($selectedStatus = old('status', $application->status?->value))
        <select id="status" name="status" required
            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (\App\Enums\ApplicationStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="applied_on" value="Date applied" />
        <x-text-input id="applied_on" name="applied_on" type="date" class="mt-1 block w-full"
            :value="old('applied_on', $application->applied_on?->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('applied_on')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="follow_up_on" value="Follow up on" />
        <x-text-input id="follow_up_on" name="follow_up_on" type="date" class="mt-1 block w-full"
            :value="old('follow_up_on', $application->follow_up_on?->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('follow_up_on')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="location" value="Location" />
        <x-text-input id="location" name="location" type="text" class="mt-1 block w-full"
            :value="old('location', $application->location)" placeholder="e.g. Remote, Kuala Lumpur" />
        <x-input-error :messages="$errors->get('location')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="job_url" value="Job posting URL" />
        <x-text-input id="job_url" name="job_url" type="url" class="mt-1 block w-full"
            :value="old('job_url', $application->job_url)" placeholder="https://" />
        <x-input-error :messages="$errors->get('job_url')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="notes" value="Notes" />
        <textarea id="notes" name="notes" rows="4"
            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $application->notes) }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>
</div>
