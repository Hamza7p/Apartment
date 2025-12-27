<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User|int $user,
        public string $type,
        public string $title,
        public string $body,
        public ?array $data = null,
        public ?string $fcmToken = null
    ) {
        // If user is an ID, we'll resolve it in handle()
        if (is_int($user)) {
            $this->user = $user;
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Resolve user if it's an ID
            if (is_int($this->user)) {
                $user = User::find($this->user);
                if (! $user) {
                    Log::warning("User not found for notification: {$this->user}");

                    return;
                }
            } else {
                $user = $this->user;
            }

            // Create notification in database
            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => $this->type,
                'title' => $this->title,
                'body' => $this->body,
                'data' => $this->data,
                'fcm_token' => $this->fcmToken ?? $user->fcm_token ?? null,
                'fcm_sent' => false,
            ]);

            // TODO: Send FCM notification in the future
            // For now, we just store it in the database
            // When FCM is ready, uncomment and implement:
            // $this->sendFcmNotification($notification, $user);

            Log::info('Notification created successfully', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'type' => $this->type,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'user_id' => is_int($this->user) ? $this->user : $this->user->id,
                'type' => $this->type,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Send FCM notification (to be implemented in the future).
     */
    protected function sendFcmNotification(Notification $notification, User $user): void
    {
        // TODO: Implement FCM notification sending
        // Example structure:
        /*
        try {
            $fcmToken = $notification->fcm_token ?? $user->fcm_token;

            if (!$fcmToken) {
                Log::warning("No FCM token found for user", ['user_id' => $user->id]);
                return;
            }

            // Use Firebase Admin SDK or HTTP client to send notification
            // $response = $fcmService->send($fcmToken, [
            //     'title' => $notification->title,
            //     'body' => $notification->body,
            //     'data' => $notification->data,
            // ]);

            $notification->update([
                'fcm_sent' => true,
                'fcm_sent_at' => now(),
            ]);

        } catch (\Exception $e) {
            $notification->update([
                'fcm_error' => $e->getMessage(),
            ]);

            Log::error("FCM notification failed", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
        }
        */
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendNotificationJob failed', [
            'user_id' => is_int($this->user) ? $this->user : $this->user->id,
            'type' => $this->type,
            'error' => $exception->getMessage(),
        ]);
    }
}
