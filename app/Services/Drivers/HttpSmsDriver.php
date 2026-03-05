<?php

declare(strict_types=1);

namespace App\Services\Drivers;

class HttpSmsDriver
{
    private string $endpoint;
    private array $headers;
    private array $payloadConfig;
    private string $senderId;

    public function __construct()
    {
        $config = require BASE_PATH . '/config/sms.php';
        
        $this->endpoint      = $config['endpoint'] ?? '';
        $this->headers       = $config['headers'] ?? [];
        $this->payloadConfig = $config['payload'] ?? ['to' => '{phone}', 'message' => '{message}'];
        $this->senderId      = $config['sender_id'] ?? 'PATS';

        // Replace any {api_key} tags in headers
        $apiKey = $config['api_key'] ?? '';
        foreach ($this->headers as &$val) {
            $val = str_replace('{api_key}', $apiKey, $val);
        }
    }

    /**
     * Send SMS via generic HTTP POST request.
     * 
     * @return array [bool success, string responseContext]
     */
    public function send(string $phone, string $message): array
    {
        if (empty($this->endpoint)) {
            return [true, 'HTTP Driver: No endpoint configured, mimicking success (Dev mode)'];
        }

        // Build Payload mapping "{phone}", "{message}" etc. to real values
        $payload = [];
        foreach ($this->payloadConfig as $key => $val) {
            if (is_string($val)) {
                $payload[$key] = str_replace(
                    ['{phone}', '{message}', '{sender_id}'], 
                    [$phone, $message, $this->senderId], 
                    $val
                );
            } else {
                $payload[$key] = $val;
            }
        }

        // Prepare curl headers
        $curlHeaders = [];
        foreach ($this->headers as $k => $v) {
            $curlHeaders[] = "{$k}: {$v}";
        }

        $ch = curl_init($this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        $isSuccess = $httpCode >= 200 && $httpCode < 300;
        
        return [
            $isSuccess, 
            $error ?: ($response ?: "HTTP $httpCode")
        ];
    }
}
