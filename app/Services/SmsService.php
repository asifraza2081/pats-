<?php

namespace App\Services;

use App\Models\NotificationsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(string $phone, string $message, ?int $userId = null, string $eventType = 'general'): bool
    {
        $baseUrl  = config('services.sms.base_url');
        $username = config('services.sms.username');
        $password = config('services.sms.password');
        $sender   = config('services.sms.sender_id', 'PATS');

        $status = 'failed';
        $sent   = false;

        try {
            if ($baseUrl) {
                $response = Http::timeout(10)->post($baseUrl, [
                    'username' => $username,
                    'password' => $password,
                    'to'       => $phone,
                    'from'     => $sender,
                    'message'  => $message,
                ]);
                $sent   = $response->successful();
                $status = $sent ? 'sent' : 'failed';
            } else {
                // Local dev: just log
                Log::channel('single')->info("[SMS to {$phone}]: {$message}");
                $sent   = true;
                $status = 'sent';
            }
        } catch (\Exception $e) {
            Log::error("SMS failed to {$phone}: " . $e->getMessage());
        }

        \App\Models\NotificationsLog::create([
            'user_id'    => $userId,
            'channel'    => 'sms',
            'recipient'  => $phone,
            'event_type' => $eventType,
            'message'    => $message,
            'status'     => $status,
            'sent_at'    => $sent ? now() : null,
            'created_at' => now(),
        ]);

        return $sent;
    }
}
