<?php

namespace App\Policies;

use App\Models\WorkExperience;
use App\Models\User;

class WorkExperiencePolicy
{
    /**
     * Determine whether the user can update the work experience record.
     */
    public function update(User $user, WorkExperience $experience): bool
    {
        if ($user->hasAnyRole(['admin', 'super_admin', 'data_entry'])) {
            return true;
        }
        return $user->candidate && $user->candidate->id === $experience->candidate_id 
               && !$user->candidate->profile_locked;
    }

    /**
     * Determine whether the user can delete the work experience record.
     */
    public function delete(User $user, WorkExperience $experience): bool
    {
        if ($user->hasAnyRole(['admin', 'super_admin'])) {
            return true;
        }
        return $user->candidate && $user->candidate->id === $experience->candidate_id 
               && !$user->candidate->profile_locked;
    }
}
