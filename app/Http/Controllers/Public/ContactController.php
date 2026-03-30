<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email|max:120',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|min:10|max:2000',
        ]);

        // Log the inquiry for now; email integration can be added later
        \Log::info('Contact Form Submission', [
            'name'    => $request->name,
            'email'   => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'ip'      => $request->ip(),
            'at'      => now()->toDateTimeString(),
        ]);

        return redirect()->route('contact')
            ->with('success', 'Thank you for reaching out, ' . $request->name . '. We have received your message and will respond within 1–2 business days.');
    }
}
