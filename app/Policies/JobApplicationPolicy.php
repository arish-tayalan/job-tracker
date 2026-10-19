<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;

/**
 * Users may only touch their own applications.
 * Laravel finds this policy automatically because of its name.
 */
class JobApplicationPolicy
{
    public function view(User $user, JobApplication $jobApplication): bool
    {
        return $jobApplication->user_id === $user->id;
    }

    public function update(User $user, JobApplication $jobApplication): bool
    {
        return $jobApplication->user_id === $user->id;
    }

    public function delete(User $user, JobApplication $jobApplication): bool
    {
        return $jobApplication->user_id === $user->id;
    }
}
