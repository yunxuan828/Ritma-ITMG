<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ITFileSharing;

class ITFileSharingPolicy
{
    public function view(User $user)
    {
        return true; // All authenticated users can view
    }

    public function create(User $user)
    {
        return $user->hasRole('admin'); // Only admins can upload
    }

    public function update(User $user, ITFileSharing $itFileSharing)
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, ITFileSharing $itFileSharing)
    {
        return $user->hasRole('admin');
    }
}