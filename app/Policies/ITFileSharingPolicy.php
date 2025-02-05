<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ITFileSharing;

class ITFileSharingPolicy
{
    public function view(User $user)
    {
        return $user->isSuperUser(); // Only superusers can view
    }

    public function create(User $user)
    {
        return $user->isSuperUser(); // Only superusers can upload
    }

    public function update(User $user, ITFileSharing $itFileSharing)
    {
        return $user->isSuperUser();
    }

    public function delete(User $user, ITFileSharing $itFileSharing)
    {
        return $user->isSuperUser();
    }
}
