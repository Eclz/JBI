<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupportMessage;

class SupportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function contact()
    {
        return view('support.contact');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'priority' => 'required|in:low,medium,high',
        ]);

        // Here you would typically send an email or create a support ticket
        // For now, we'll just redirect with a success message

        return redirect()->route('support.contact')
            ->with('success', 'Your support request has been submitted. We will get back to you soon.');
    }
}
