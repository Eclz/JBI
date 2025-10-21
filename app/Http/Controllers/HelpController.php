<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $helpTopics = [
            'getting-started' => 'Getting Started',
            'courses' => 'Course Management',
            'assignments' => 'Assignments',
            'grades' => 'Grades and Transcripts',
            'fees' => 'Fees and Payments',
            'profile' => 'Profile Management',
            'technical-support' => 'Technical Support',
        ];

        return view('help.index', compact('helpTopics'));
    }

    public function show($topic)
    {
        $validTopics = [
            'getting-started',
            'courses',
            'assignments',
            'grades',
            'fees',
            'profile',
            'technical-support'
        ];

        if (!in_array($topic, $validTopics)) {
            abort(404);
        }

        return view("help.topics.{$topic}");
    }
}
