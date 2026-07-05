<?php

namespace App\Support;

use App\Models\User;

class RoleRouter
{
    /** Route name to redirect a user to after login, based on role. */
    public static function homeRouteFor(User $user): string
    {
        return match ($user->role) {
            'customer' => 'home',
            'courier' => 'courier.tasks',
            'operator' => 'operator.board',
            'outlet_admin' => 'operator.board',
            'owner' => 'owner.dashboard',
            default => 'home',
        };
    }
}
