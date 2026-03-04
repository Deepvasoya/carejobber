<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AdminAuthorizationHelper
{

    public static function check($allowed_roles = ['SUP_ADM'])
    {
        if (null === Auth::guard('admin')->user()) {
            return false;
        }
        $user = Auth::guard('admin')->user();
        $userRole = $user->getAdminUserRole();
        if (!$userRole) {
            return false;
        }
        $roles = is_array($allowed_roles) ? $allowed_roles : [$allowed_roles];
        return in_array($userRole->role_abbreviation, $roles) ? true : false;
    }

    /**
     * Check if the current admin has the given permission (by slug).
     */
    public static function can($slug)
    {
        $user = Auth::guard('admin')->user();
        if (!$user) {
            return false;
        }
        return $user->hasPermission($slug);
    }
}
