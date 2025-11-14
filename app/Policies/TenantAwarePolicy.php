<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantAwarePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Users can view models from their own tenant
        return $user->tenant_id !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, $model): bool
    {
        // For tenant-aware models, check if model belongs to user's tenant
        if (method_exists($model, 'getAttribute') && $model->getAttribute('tenant_id')) {
            return $model->getAttribute('tenant_id') == $user->tenant_id;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, $model): bool
    {
        // For tenant-aware models, check if model belongs to user's tenant
        if (method_exists($model, 'getAttribute') && $model->getAttribute('tenant_id')) {
            return $model->getAttribute('tenant_id') == $user->tenant_id;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, $model): bool
    {
        // For tenant-aware models, check if model belongs to user's tenant
        if (method_exists($model, 'getAttribute') && $model->getAttribute('tenant_id')) {
            return $model->getAttribute('tenant_id') == $user->tenant_id;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, $model): bool
    {
        return $this->delete($user, $model);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, $model): bool
    {
        return $this->delete($user, $model);
    }
}