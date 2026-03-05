<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SmsLog;
use App\Services\Drivers\HttpSmsDriver;

class SmsService
{
    private HttpSmsDriver $driver;

    public function __construct()
    {
        $this->driver = new HttpSmsDriver();
    }

    /**
     * Send instant SMS and log it.
     */
    public function send(string $phone, string $message, string $eventType = null): bool
    {
        // Add international prefix to pk phone if missing, driver can handle this normally.
        $cleanPhone = preg_replace('/[^0-9\+]/', '', $phone);
        
        // Dispath to generic HTTP driver
        [$success, $response] = $this->driver->send($cleanPhone, $message);

        // Log to db
        SmsLog::log([
            'recipient'         => $cleanPhone,
            'message'           => $message,
            'event_type'        => $eventType,
            'status'            => $success ? 'sent' : 'failed',
            'provider_response' => substr($response, 0, 1000)
        ]);

        return $success;
    }

    /**
     * Queue a bulk SMS. Used by admin dashboard.
     */
    public function queueBulk(array $phones, string $message, ?int $projectId = null): void
    {
        $db = \App\Core\Database::getInstance();
        $db->beginTransaction();

        try {
            foreach ($phones as $phone) {
                $db->insert('sms_queue', [
                    'project_id' => $projectId,
                    'recipient'  => preg_replace('/[^0-9\+]/', '', $phone),
                    'message'    => $message,
                    'status'     => 'pending',
                ]);
            }
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            error_log('Failed to queue bulk SMS: ' . $e->getMessage());
        }
    }
}
