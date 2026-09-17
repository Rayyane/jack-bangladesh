<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EnquiryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'source' => ['required', 'in:footer,contact'],
        ]);

        $subject = ($validated['source'] === 'footer' ? 'Website quick inquiry' : 'Website contact enquiry')
            .($validated['subject'] ? ': '.$validated['subject'] : '');

        Mail::raw(implode("\n\n", array_filter([
            'Source: '.ucfirst($validated['source']),
            'Name: '.($validated['name'] ?? 'Not provided'),
            'Email: '.$validated['email'],
            isset($validated['phone']) && $validated['phone'] !== '' ? 'Phone: '.$validated['phone'] : null,
            'Message:',
            $validated['message'],
        ])), function ($mail) use ($validated, $subject): void {
            $mail->to('info@jack.com.bd')
                ->subject($subject)
                ->replyTo($validated['email'], $validated['name'] ?? null);
        });

        return back()->with('success', 'Thank you. Your enquiry has been sent.');
    }
}
