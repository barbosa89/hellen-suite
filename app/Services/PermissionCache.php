<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class PermissionCache
{
    public static function get(): array
    {
        if (Auth::check()) {
            $permissions = Cache::remember('user-' . Auth::id(), 60 * 60 * 8, function () {
                return Permission::whereHas('users', function ($query) {
                    $query->where('id', Auth::id());
                })->get(['id', 'name']);
            });

            return $permissions->pluck('name')->toArray();
        }

        return [];
    }

    public static function forget(User $user): bool
    {
        return Cache::forget('user-' . $user->id);
    }
}
