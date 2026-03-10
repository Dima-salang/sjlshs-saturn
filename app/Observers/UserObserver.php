<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if ($user->workos_id) {
            $isAdmin = $user->email === config('app.admin_email');

            if ($isAdmin) {
                $user->updateQuietly([
                    'is_active' => true,
                    'is_admin' => true,
                ]);
            }

            $user->teacher()->create([
                'user_id' => $user->workos_id,
                'full_name' => $user->name,
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged('name')) {
            $user->teacher()->update([
                'full_name' => $user->name,
            ]);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if ($user->workos_id) {
            $user->teacher()->delete();
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
