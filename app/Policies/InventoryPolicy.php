<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;

class InventoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role->isInventory();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Inventory $inventory): bool
    {
        return $user->role->isInventory();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role->isAddInventory();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Inventory $inventory): bool
    {
        return $user->role->isUpdateInventory();
    }
}
