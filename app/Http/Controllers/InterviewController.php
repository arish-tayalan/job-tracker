<?php

namespace App\Http\Controllers;

use App\Http\Requests\InterviewRequest;
use App\Models\Interview;
use App\Models\JobApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class InterviewController extends Controller
{
    public function store(InterviewRequest $request, JobApplication $jobApplication): RedirectResponse
    {
        // Only the owner of the application may add interviews to it.
        Gate::authorize('update', $jobApplication);

        // Creating through the relationship sets job_application_id for us.
        $jobApplication->interviews()->create($request->validated());

        return redirect()->route('job-applications.show', $jobApplication)
            ->with('status', 'Interview added.');
    }

    public function destroy(Interview $interview): RedirectResponse
    {
        $application = $interview->jobApplication;

        Gate::authorize('update', $application);

        $interview->delete();

        return redirect()->route('job-applications.show', $application)
            ->with('status', 'Interview deleted.');
    }
}
