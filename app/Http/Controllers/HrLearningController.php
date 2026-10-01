<?php

namespace App\Http\Controllers;

use App\Models\HrTrainingCourse;
use App\Models\HrTrainingEnrollment;
use Illuminate\Http\Request;

class HrLearningController extends Controller
{
    public function storeCourse(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'provider' => 'nullable|string|max:150',
            'duration_hours' => 'required|numeric|min:0.5',
            'cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $data['status'] = 'Active';

        HrTrainingCourse::create($data);

        return back()->with('success', 'Training course created successfully.');
    }

    public function enrollUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'hr_training_course_id' => 'required|exists:hr_training_courses,id',
            'enrollment_date' => 'required|date',
        ]);

        $existing = HrTrainingEnrollment::where('user_id', $request->user_id)
            ->where('hr_training_course_id', $request->hr_training_course_id)
            ->whereNotIn('status', ['Failed', 'Cancelled'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Employee is already enrolled in this course.');
        }

        HrTrainingEnrollment::create([
            'user_id' => $request->user_id,
            'hr_training_course_id' => $request->hr_training_course_id,
            'enrollment_date' => $request->enrollment_date,
            'status' => 'Enrolled',
        ]);

        return back()->with('success', 'Employee enrolled in training course successfully.');
    }

    public function updateEnrollment(Request $request, HrTrainingEnrollment $enrollment)
    {
        $data = $request->validate([
            'status' => 'required|in:Enrolled,In Progress,Completed,Failed,Cancelled',
        ]);

        if ($data['status'] === 'Completed') {
            $data['completion_date'] = now();
        }

        $enrollment->update($data);

        return back()->with('success', 'Training enrollment status updated.');
    }
}
