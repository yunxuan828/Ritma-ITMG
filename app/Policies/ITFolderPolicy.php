<?php

namespace App\Policies;

use App\Models\ITFolder;
use App\Models\User;

class ITFolderPolicy
{
    /**
     * Determine if the user can view any folders.
     */
    public function viewAny(User $user): bool
    {
        return true; // All users can view folders
    }

    /**
     * Determine if the user can create folders.
     */
    public function create(User $user): bool
    {
        return $user->hasAccess('admin'); // Only admins can create folders
    }

    /**
     * Determine if the user can update the folder.
     */
    public function update(User $user, ITFolder $folder): bool
    {
        return $user->hasAccess('admin'); // Only admins can update folders
    }

    /**
     * Determine if the user can delete the folder.
     */
    public function delete(User $user, ITFolder $folder): bool
    {
        // Only admins can delete folders, and only if they're empty
        return $user->hasAccess('admin') && $folder->canDelete();
    }
}
