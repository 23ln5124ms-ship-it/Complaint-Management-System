<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    public function view(User $user, Complaint $complaint): bool
    {
        return $user->isAdmin() || $complaint->user_id === $user->id;
    }

    public function update(User $user, Complaint $complaint): bool
    {
        if ($user->isAdmin()) return true;
        // Users can only edit pending complaints
        return $complaint->user_id === $user->id && $complaint->status === 'pending';
    }

    public function delete(User $user, Complaint $complaint): bool
    {
        if ($user->isAdmin()) return true;
        return $complaint->user_id === $user->id && $complaint->status === 'pending';
    }
}
