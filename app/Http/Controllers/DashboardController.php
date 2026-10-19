<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Models\Interview;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** How many weeks the "applications per week" chart covers. */
    private const WEEKS = 8;

    public function __invoke(Request $request): View
    {
        $user = $request->user();

        // ['applied' => 4, 'offer' => 1, ...]
        $byStatus = $user->jobApplications()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $total = $byStatus->sum();
        $active = ($byStatus[ApplicationStatus::Applied->value] ?? 0)
            + ($byStatus[ApplicationStatus::Interviewing->value] ?? 0);

        // "Submitted" = everything except jobs you only saved to your wishlist.
        $submitted = $total - ($byStatus[ApplicationStatus::Wishlist->value] ?? 0);

        // Got an interview = reached the Interviewing/Offer stage OR has at least one interview logged.
        $gotInterview = $user->jobApplications()
            ->where('status', '!=', ApplicationStatus::Wishlist->value)
            ->where(fn ($q) => $q
                ->whereIn('status', [ApplicationStatus::Interviewing->value, ApplicationStatus::Offer->value])
                ->orHas('interviews'))
            ->count();

        $interviewRate = $submitted > 0 ? (int) round($gotInterview / $submitted * 100) : null;

        $upcomingInterviews = Interview::query()
            ->whereRelation('jobApplication', 'user_id', $user->id)
            ->where('scheduled_at', '>=', now())
            ->with('jobApplication')
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $followUps = $user->jobApplications()
            ->whereIn('status', [ApplicationStatus::Applied->value, ApplicationStatus::Interviewing->value])
            ->whereDate('follow_up_on', '<=', today())
            ->orderBy('follow_up_on')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'total' => $total,
            'active' => $active,
            'offers' => $byStatus[ApplicationStatus::Offer->value] ?? 0,
            'interviewRate' => $interviewRate,
            'upcomingInterviews' => $upcomingInterviews,
            'followUps' => $followUps,
            'chartData' => [
                'status' => $this->statusChart($byStatus),
                'weekly' => $this->weeklyChart($request),
            ],
        ]);
    }

    /** Doughnut chart: one slice per status that has at least one application. */
    private function statusChart($byStatus): array
    {
        $colors = [
            'wishlist' => '#9ca3af', 'applied' => '#3b82f6', 'interviewing' => '#f59e0b',
            'offer' => '#22c55e', 'rejected' => '#ef4444', 'withdrawn' => '#64748b',
        ];

        $labels = $values = $sliceColors = [];
        foreach (ApplicationStatus::cases() as $status) {
            $count = $byStatus[$status->value] ?? 0;
            if ($count > 0) {
                $labels[] = $status->label();
                $values[] = $count;
                $sliceColors[] = $colors[$status->value];
            }
        }

        return ['labels' => $labels, 'values' => $values, 'colors' => $sliceColors];
    }

    /** Bar chart: applications per week for the last WEEKS weeks (weeks start on Monday). */
    private function weeklyChart(Request $request): array
    {
        $firstWeek = today()->startOfWeek()->subWeeks(self::WEEKS - 1);

        // Count in PHP rather than SQL so it works on any database.
        $perWeek = $request->user()->jobApplications()
            ->whereDate('applied_on', '>=', $firstWeek)
            ->pluck('applied_on')
            ->countBy(fn ($date) => $date->copy()->startOfWeek()->toDateString());

        $labels = $values = [];
        for ($i = 0; $i < self::WEEKS; $i++) {
            $week = $firstWeek->copy()->addWeeks($i);
            $labels[] = $week->format('d M');
            $values[] = $perWeek[$week->toDateString()] ?? 0;
        }

        return ['labels' => $labels, 'values' => $values];
    }
}
