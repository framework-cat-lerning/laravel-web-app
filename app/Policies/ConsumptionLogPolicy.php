<?php

namespace App\Policies;

use App\Models\ConsumptionLog;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConsumptionLogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ConsumptionLog $consumptionLog): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role->isUpdateInventory();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ConsumptionLog $consumptionLog): bool
    {
        return $user->role->isUpdateInventory();
    }
}
