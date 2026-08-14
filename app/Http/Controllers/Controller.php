<?php

namespace App\Http\Controllers;

use App\Models\User;

abstract class Controller
{
    /**
     * Return the authenticated application user.
     */
    protected function authenticatedUser(): User
    {
        $user = auth()->user();

        abort_unless($user instanceof User, 401);

        return $user;
    }
}
