<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('support.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'priority' => 'required|in:low,medium,high',
        ]);

        \Log::info('Support Request', [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'subject' => $request->subject,
            'priority' => $request->priority,
            'message' => $request->message,
        ]);

        // Here you would typically send an email or create a support ticket
        // For now, we'll just redirect with a success message

        return redirect()->route('support.index')
            ->with('success', 'Your support request has been submitted. We will get back to you soon.');
    }
}
