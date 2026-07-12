<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TutorSalary;
use Illuminate\Auth\Access\HandlesAuthorization;

class TutorSalaryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TutorSalary');
    }

    public function view(AuthUser $authUser, TutorSalary $tutorSalary): bool
    {
        return $authUser->can('View:TutorSalary');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TutorSalary');
    }

    public function update(AuthUser $authUser, TutorSalary $tutorSalary): bool
    {
        return $authUser->can('Update:TutorSalary');
    }

    public function delete(AuthUser $authUser, TutorSalary $tutorSalary): bool
    {
        return $authUser->can('Delete:TutorSalary');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TutorSalary');
    }

    public function restore(AuthUser $authUser, TutorSalary $tutorSalary): bool
    {
        return $authUser->can('Restore:TutorSalary');
    }

    public function forceDelete(AuthUser $authUser, TutorSalary $tutorSalary): bool
    {
        return $authUser->can('ForceDelete:TutorSalary');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TutorSalary');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TutorSalary');
    }

    public function replicate(AuthUser $authUser, TutorSalary $tutorSalary): bool
    {
        return $authUser->can('Replicate:TutorSalary');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TutorSalary');
    }

}