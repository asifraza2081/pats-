<?php

namespace App\Policies;

use App\Models\EducationHistory;
use App\Models\User;

class EducationHistoryPolicy
{
    /**
     * Determine whether the user can update the education record.
     */
    public function update(User $user, EducationHistory $education): bool
    {
        if ($user->hasAnyRole(['admin', 'super_admin', 'data_entry'])) {
            return true;
        }
        return $user->candidate && $user->candidate->id === $education->candidate_id 
               && !$user->candidate->profile_locked;
    }

    /**
     * Determine whether the user can delete the education record.
     */
    public function delete(User $user, EducationHistory $education): bool
    {
        if ($user->hasAnyRole(['admin', 'super_admin'])) {
            return true;
        }
        return $user->candidate && $user->candidate->id === $education->candidate_id 
               && !$user->candidate->profile_locked;
    }
}
