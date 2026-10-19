<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class BoardController extends Controller
{
    /**
     * Kanban board: the user's applications grouped into one column per status.
     */
    public function __invoke(Request $request): View
    {
        // groupBy() on a collection gives ['applied' => [app, app], 'offer' => [app], ...]
        $columns = $request->user()->jobApplications()
            ->latest('updated_at')
            ->get()
            ->groupBy(fn ($application) => $application->status->value);

        return view('board', ['columns' => $columns]);
    }
}
