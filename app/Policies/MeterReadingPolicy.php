<?php

namespace App\Policies;

use App\Models\MeterReading;
use App\Models\User;

class MeterReadingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_reading');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MeterReading $meterReading): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('view_reading') && $meterReading->property->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_reading');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MeterReading $meterReading): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('update_reading') && $meterReading->property->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MeterReading $meterReading): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete_reading') && $meterReading->property->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MeterReading $meterReading): bool
    {
        return $user->can('delete_reading');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MeterReading $meterReading): bool
    {
        return $user->can('delete_reading');
    }
}