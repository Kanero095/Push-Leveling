<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Notifications extends Component
{
    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($notificationId)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($notificationId);

        $notification->update(['is_read' => true]);

        $this->dispatch('toast', variant: 'success', text: 'Notifikasi ditandai sebagai dibaca.');
    }

    /**
     * Mark all user's notifications as read.
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $this->dispatch('toast', variant: 'success', text: 'Semua notifikasi ditandai sebagai dibaca.');
    }

    /**
     * Delete a specific notification.
     */
    public function deleteNotification($notificationId)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($notificationId);

        $notification->delete();

        $this->dispatch('toast', variant: 'info', text: 'Notifikasi berhasil dihapus.');
    }

    /**
     * Delete all notifications for the user.
     */
    public function clearAll()
    {
        Notification::where('user_id', Auth::id())->delete();

        $this->dispatch('toast', variant: 'info', text: 'Semua riwayat notifikasi dihapus.');
    }

    /**
     * Render the Livewire component.
     */
    public function render()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->get();

        $unreadCount = $notifications->where('is_read', false)->count();

        return view('livewire.notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ])->layout('layouts.app', ['title' => 'Notifikasi']);
    }
}
