<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Http\Requests\JobApplicationRequest;
use App\Models\JobApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class JobApplicationController extends Controller
{
    /**
     * List the logged-in user's applications, optionally filtered by ?status=...
     */
    public function index(Request $request): View
    {
        // tryFrom() returns null for a missing or unknown status, which means "show all".
        $status = ApplicationStatus::tryFrom((string) $request->query('status'));

        // ?search=... matches company or position. Empty string means "no search".
        $search = trim((string) $request->query('search'));

        // ?sort=company&dir=asc. Only allow known columns: the column name goes into the SQL,
        // so never pass user input straight to orderBy().
        $sortable = ['company', 'position', 'status', 'location', 'applied_on'];
        $sort = in_array($request->query('sort'), $sortable, true) ? $request->query('sort') : 'applied_on';
        $direction = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $applications = $request->user()->jobApplications()
            ->when($status, fn ($query) => $query->where('status', $status))
            // The inner closure puts the OR inside brackets, so it can't escape the user_id rule:
            // user_id = ? AND (company LIKE ? OR position LIKE ?)
            ->when($search !== '', fn ($query) => $query->where(function ($q) use ($search) {
                $q->where('company', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            }))
            ->orderBy($sort, $direction)
            ->orderByDesc('id') // tie-breaker so rows with equal values keep a stable order
            ->paginate(10)
            ->withQueryString(); // keep ?status=, ?search= and ?sort= on the page links

        // Counts for the filter tabs, e.g. ['applied' => 4, 'offer' => 1]
        $counts = $request->user()->jobApplications()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('job-applications.index', [
            'applications' => $applications,
            'counts' => $counts,
            'currentStatus' => $status,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create(): View
    {
        return view('job-applications.create', [
            'application' => new JobApplication([
                'status' => ApplicationStatus::Applied,
                'applied_on' => now(),
            ]),
        ]);
    }

    public function store(JobApplicationRequest $request): RedirectResponse
    {
        // Creating through the relationship sets user_id for us.
        $request->user()->jobApplications()->create($request->validated());

        return redirect()->route('job-applications.index')
            ->with('status', 'Application added.');
    }

    public function show(JobApplication $jobApplication): View
    {
        Gate::authorize('view', $jobApplication);

        return view('job-applications.show', [
            'application' => $jobApplication,
        ]);
    }

    public function edit(JobApplication $jobApplication): View
    {
        Gate::authorize('update', $jobApplication);

        return view('job-applications.edit', [
            'application' => $jobApplication,
        ]);
    }

    public function update(JobApplicationRequest $request, JobApplication $jobApplication): RedirectResponse
    {
        Gate::authorize('update', $jobApplication);

        $jobApplication->update($request->validated());

        return redirect()->route('job-applications.index')
            ->with('status', 'Application updated.');
    }

    public function destroy(JobApplication $jobApplication): RedirectResponse
    {
        Gate::authorize('delete', $jobApplication);

        $jobApplication->delete();

        // Not back(): if you deleted from the detail page, "back" would be a page that no longer exists.
        return redirect()->route('job-applications.index')
            ->with('status', 'Application deleted.');
    }
}
