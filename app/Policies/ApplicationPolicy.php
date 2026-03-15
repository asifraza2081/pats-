<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    /**
     * Determine whether the user can view the application.
     */
    public function view(User $user, Application $application): bool
    {
        if ($user->hasAnyRole(['admin', 'super_admin', 'data_entry'])) {
            return true;
        }
        return $user->candidate && $user->candidate->id === $application->candidate_id;
    }

    /**
     * Determine whether the user can delete the application.
     */
    public function delete(User $user, Application $application): bool
    {
        if ($user->hasAnyRole(['admin', 'super_admin'])) {
            return true;
        }
        return $user->candidate && $user->candidate->id === $application->candidate_id 
               && $application->status === \App\Enums\ApplicationStatus::SUBMITTED;
    }
}
