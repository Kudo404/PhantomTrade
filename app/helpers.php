<?php

use App\Models\AppNotification;

if (! function_exists('notify')) {
    /**
     * Send an in-app notification to a user.
     *
     * @param  int|User  $userId
     * @param  string    $title   Short header line
     * @param  string    $body    One-line description
     * @param  string    $url     Where clicking navigates to (nullable)
     * @param  string    $icon    Emoji shown in the notification
     */
    function notify($userId, string $title, string $body, ?string $url = null, string $icon = '🔔'): AppNotification
    {
        // Accept a User instance too
        if ($userId instanceof \App\Models\User) {
            $userId = $userId->id;
        }

        return AppNotification::create([
            'user_id' => $userId,
            'title'   => $title,
            'body'    => $body,
            'icon'    => $icon,
            'url'     => $url,
        ]);
    }
}

if (! function_exists('notifyAdmins')) {
    /**
     * Send the same notification to every admin.
     */
    function notifyAdmins(string $title, string $body, ?string $url = null, string $icon = '🔔'): void
    {
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            notify($admin->id, $title, $body, $url, $icon);
        }
    }
}