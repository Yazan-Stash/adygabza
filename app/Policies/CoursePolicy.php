<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Course;

class CoursePolicy
{
    public function viewAny(Admin $user): bool
    {
        return true;
    }

    public function view(Admin $user, Course $course): bool
    {
        return true;
    }

    public function create(Admin $user): bool
    {
        return true;
    }

    public function update(Admin $user, Course $course): bool
    {
        return true;
    }

    public function delete(Admin $user, Course $course): bool
    {
        return true;
    }

    public function restore(Admin $user, Course $course): bool
    {
        return true;
    }

    public function forceDelete(Admin $user, Course $course): bool
    {
        return true;
    }
}
