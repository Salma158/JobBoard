<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;



class ApplicationController extends Controller
{
    public function index()
    {
        return Inertia::render('Applications/submit');
    }

    public function store(Request $request)
    {
        $userID = Auth::id();

        $request->validate([
            'jobId' => 'required',
        ]);

        Application::create([
            'user_id' => $userID,
            'job_id' => $request->jobId,
            'emp_id' => $request->empId,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard')->with('success', 'Application created successfully!');
    }

    public function show()
    {
        $employerId = Auth::id();
        $applications = Application::where('emp_id', $employerId)
            ->with('job', 'candidate')
            ->get();

        $userApplications = $applications->map(function ($application) {
            return [
                'candidate_name' => $application->candidate->name,
                'job_title' => $application->job->title,
                'candidate_email' => $application->candidate->email,
                'date_of_application' => $application->created_at->toDateString(),
                'status' => $application->status,
            ];
        });
        return Inertia::render('Applications/show', ['userApplications' => $userApplications]);
    }
}