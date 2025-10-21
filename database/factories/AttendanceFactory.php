<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Course;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $student = User::where('role', 'student')->inRandomOrder()->first() 
                  ?? User::factory()->student()->create();
        $course = Course::inRandomOrder()->first() ?? Course::factory()->create();

        $attendanceDate = fake()->dateTimeBetween('-60 days', 'now');
        $classStartTime = fake()->dateTimeBetween($attendanceDate->format('Y-m-d') . ' 08:00', $attendanceDate->format('Y-m-d') . ' 16:00');
        $classEndTime = (clone $classStartTime)->modify('+90 minutes');

        $status = fake()->randomElement(['present', 'present', 'present', 'present', 'late', 'absent', 'excused']);
        
        $checkInTime = null;
        $checkOutTime = null;
        $minutesLate = 0;

        if ($status === 'present') {
            $checkInTime = fake()->dateTimeBetween($classStartTime->format('Y-m-d H:i:s'), $classStartTime->modify('+10 minutes')->format('Y-m-d H:i:s'));
            $checkOutTime = fake()->optional(0.8)->dateTimeBetween($classEndTime->modify('-10 minutes')->format('Y-m-d H:i:s'), $classEndTime->format('Y-m-d H:i:s'));
        } elseif ($status === 'late') {
            $minutesLate = fake()->numberBetween(5, 30);
            $checkInTime = (clone $classStartTime)->modify("+{$minutesLate} minutes");
            $checkOutTime = fake()->optional(0.8)->dateTimeBetween($classEndTime->modify('-10 minutes')->format('Y-m-d H:i:s'), $classEndTime->format('Y-m-d H:i:s'));
        }

        return [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'attendance_date' => $attendanceDate->format('Y-m-d'),
            'class_start_time' => $classStartTime,
            'class_end_time' => $classEndTime,
            'status' => $status,
            'check_in_time' => $checkInTime,
            'check_out_time' => $checkOutTime,
            'minutes_late' => $minutesLate,
            'notes' => fake()->optional(0.2)->sentence(),
            'check_in_method' => fake()->randomElement(['manual', 'qr_code', 'biometric']),
            'marked_by' => $course->instructor_id,
        ];
    }

    /**
     * Create present attendance
     */
    public function present(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'present',
        ]);
    }

    /**
     * Create absent attendance
     */
    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'absent',
            'check_in_time' => null,
            'check_out_time' => null,
            'minutes_late' => 0,
        ]);
    }

    /**
     * Create late attendance
     */
    public function late(): static
    {
        return $this->state(function (array $attributes) {
            $minutesLate = fake()->numberBetween(5, 30);
            $classStartTime = $attributes['class_start_time'];
            $checkInTime = (clone $classStartTime)->modify("+{$minutesLate} minutes");
            
            return [
                'status' => 'late',
                'check_in_time' => $checkInTime,
                'minutes_late' => $minutesLate,
            ];
        });
    }
}
