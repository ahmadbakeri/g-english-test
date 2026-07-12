<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Honor;
use Illuminate\Auth\Access\HandlesAuthorization;

class HonorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Honor');
    }

    public function view(AuthUser $authUser, Honor $honor): bool
    {
        return $authUser->can('View:Honor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Honor');
    }

    public function update(AuthUser $authUser, Honor $honor): bool
    {
        return $authUser->can('Update:Honor');
    }

    public function delete(AuthUser $authUser, Honor $honor): bool
    {
        return $authUser->can('Delete:Honor');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Honor');
    }

    public function restore(AuthUser $authUser, Honor $honor): bool
    {
        return $authUser->can('Restore:Honor');
    }

    public function forceDelete(AuthUser $authUser, Honor $honor): bool
    {
        return $authUser->can('ForceDelete:Honor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Honor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Honor');
    }

    public function replicate(AuthUser $authUser, Honor $honor): bool
    {
        return $authUser->can('Replicate:Honor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Honor');
    }

}