<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $input = $request->all();
            $sanitized = $this->sanitize($input);
            $request->replace($sanitized);
        }

        return $next($request);
    }

    /**
     * Recursively sanitize all input, allowing safe HTML via Purifier for long-text/rich-text.
     */
    private function sanitize(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitize($value);
            } elseif (is_string($value)) {
                // To prevent oversized string payloads DOS (e.g. max 5MB strings)
                if (strlen($value) > (5 * 1024 * 1024)) {
                    abort(413, 'Payload string exceeds maximum allowed boundaries.');
                }
                
                // Fields that should allow rich HTML explicitly:
                if (in_array(strtolower($key), ['description', 'content', 'html_body', 'instructions'])) {
                    // Pass through HTML Purifier which removes scripts, xss, but keeps formatting
                    $data[$key] = clean($value);
                } else {
                    // For standard input, completely strip ALL HTML tags and execute trim
                    $data[$key] = strip_tags(trim($value));
                }
            }
        }
        return $data;
    }
}
