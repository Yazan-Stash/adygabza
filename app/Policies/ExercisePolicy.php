<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Exercise;

class ExercisePolicy
{
    public function viewAny(Admin $user): bool
    {
        return true;
    }

    public function view(Admin $user, Exercise $exercise): bool
    {
        return true;
    }

    public function create(Admin $user): bool
    {
        return true;
    }

    public function update(Admin $user, Exercise $exercise): bool
    {
        return true;
    }

    public function delete(Admin $user, Exercise $exercise): bool
    {
        return true;
    }

    public function restore(Admin $user, Exercise $exercise): bool
    {
        return true;
    }

    public function forceDelete(Admin $user, Exercise $exercise): bool
    {
        return true;
    }
}
