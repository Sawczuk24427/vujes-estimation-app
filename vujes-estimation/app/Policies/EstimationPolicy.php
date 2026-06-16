<?php

namespace App\Policies;

use App\Models\Estimation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EstimationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Estimation $estimation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $estimation->project->client->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Estimation $estimation): bool
    {
        return $estimation->project->client->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Estimation $estimation): bool
    {
        return $estimation->project->client->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Estimation $estimation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Estimation $estimation): bool
    {
        return false;
    }
}
