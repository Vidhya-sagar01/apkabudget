<?php

if (!function_exists('hasPermission')) {
    function hasPermission(string $permission): bool
    {
        
        $user = Auth::guard('admin')->user();
        // print_r($user->role);die;

        if (!$user) {
            return false;
        }

        // If you have superadmin role that bypasses permission
        if ($user->role === 1) {
            return true;
        }

        $permissions = json_decode($user->permissions ?? '[]', true);
// dd($permissions);
        return in_array($permission, $permissions);
    }
}
