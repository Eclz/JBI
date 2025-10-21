<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_id',
        'employee_id',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'emergency_contact',
        'emergency_phone',
        'profile_picture',
        'is_active',
        'preferences',
        'last_login_at',
        'first_name',
        'last_name',
        'email_verified_at',
        'must_change_password',
        'password_changed_at',
        'default_password', // Store the raw default password for email
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'default_password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'preferences' => 'array',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'must_change_password' => 'boolean',
        'password_changed_at' => 'datetime',
    ];

    /**
     * User roles
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_FACULTY = 'faculty';
    const ROLE_STUDENT = 'student';
    const ROLE_PARENT = 'parent';

    /**
     * Get all available roles
     */
    public static function getRoles()
    {
        return [
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_FACULTY => 'Faculty',
            self::ROLE_STUDENT => 'Student',
            self::ROLE_PARENT => 'Parent',
        ];
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if user is faculty
     */
    public function isFaculty()
    {
        return $this->hasRole(self::ROLE_FACULTY);
    }

    /**
     * Check if user is student
     */
    public function isStudent()
    {
        return $this->hasRole(self::ROLE_STUDENT);
    }

    /**
     * Check if user is parent
     */
    public function isParent()
    {
        return $this->hasRole(self::ROLE_PARENT);
    }

    /**
     * Check if user must change password
     */
    public function mustChangePassword()
    {
        return $this->must_change_password;
    }

    /**
     * Generate a secure default password
     */
    public static function generateDefaultPassword()
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        $password = '';
        for ($i = 0; $i < 12; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $password;
    }

    /**
     * Generate a JBI-formatted default password
     */
    public static function generateJBIDefaultPassword()
    {
        return 'JBI' . date('Y') . Str::random(6) . '!';
    }

    /**
     * Set password and mark for change if needed
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
        $this->attributes['password_changed_at'] = now();
    }

    /**
     * Create user with default password for admission
     */
    public static function createWithDefaultPassword($userData)
    {
        $defaultPassword = self::generateJBIDefaultPassword();

        $userData['password'] = $defaultPassword;
        $userData['default_password'] = $defaultPassword; // Store for email
        $userData['must_change_password'] = true;
        $userData['is_active'] = true; // Activated upon admission approval

        return self::create($userData);
    }

    /**
     * Mark password as changed by user
     */
    public function markPasswordAsChanged()
    {
        $this->update([
            'must_change_password' => false,
            'default_password' => null, // Clear the stored default password
            'password_changed_at' => now(),
        ]);
    }

    /**
     * Get the student profile (for students)
     */
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    /**
     * Get the faculty profile (for faculty)
     */
    public function facultyProfile()
    {
        return $this->hasOne(FacultyProfile::class);
    }

    /**
     * Get the courses that the user is enrolled in (for students)
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_enrollments')
                    ->withPivot('enrollment_date', 'status', 'final_grade', 'letter_grade', 'grade_points', 'completion_date')
                    ->withTimestamps();
    }

    /**
     * Get the courses that the user teaches (for faculty)
     */
    public function teachingCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Get user's assignment submissions (for students)
     */
    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Get user's grades (for students)
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get user's attendance records
     */
    public function attendanceRecords()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get user's fee records (for students)
     */
    public function feeRecords()
    {
        return $this->hasMany(FeeRecord::class);
    }

    /**
     * Get user's notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get announcements created by user
     */
    public function createdAnnouncements()
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    /**
     * Get forum topics created by user
     */
    public function forumTopics()
    {
        return $this->hasMany(ForumTopic::class);
    }

    /**
     * Get forum replies by user
     */
    public function forumReplies()
    {
        return $this->hasMany(ForumReply::class);
    }

    /**
     * Get course materials uploaded by user
     */
    public function uploadedMaterials()
    {
        return $this->hasMany(CourseMaterial::class, 'uploaded_by');
    }

    /**
     * Get assignments graded by user (for faculty)
     */
    public function gradedAssignments()
    {
        return $this->hasMany(AssignmentSubmission::class, 'graded_by');
    }

    /**
     * Get grades given by user (for faculty)
     */
    public function givenGrades()
    {
        return $this->hasMany(Grade::class, 'graded_by');
    }

    /**
     * Get attendance records marked by user (for faculty)
     */
    public function markedAttendance()
    {
        return $this->hasMany(Attendance::class, 'marked_by');
    }

    /**
     * Get audit logs for user actions
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get departments headed by user (for faculty/admin)
     */
    public function headedDepartments()
    {
        return $this->hasMany(Department::class, 'head_of_department_id');
    }

    /**
     * Get fee records processed by user (for admin/finance)
     */
    public function processedFeeRecords()
    {
        return $this->hasMany(FeeRecord::class, 'processed_by');
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }
        return $this->name ?? $this->email;
    }

    /**
     * Get profile picture URL
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }

        $name = $this->full_name ?? $this->email;
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=1e3a8a&background=e0e7ff';
    }

    /**
     * Get application status for display
     */
    public function getApplicationStatusAttribute()
    {
        if ($this->isStudent() && $this->studentProfile) {
            return $this->studentProfile->application_status ?? 'pending';
        }

        if ($this->isFaculty() && $this->facultyProfile) {
            return $this->facultyProfile->employment_status ?? 'pending';
        }

        return $this->is_active ? 'active' : 'pending';
    }

    /**
     * Check if user's application is pending
     */
    public function isApplicationPending()
    {
        return !$this->is_active && !$this->hasVerifiedEmail();
    }

    /**
     * Check if user's application is approved
     */
    public function isApplicationApproved()
    {
        return $this->is_active && $this->hasVerifiedEmail();
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for users by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for verified users
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope for unverified users
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * Scope for pending applications
     */
    public function scopePendingApplications($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for users who must change password
     */
    public function scopeMustChangePassword($query)
    {
        return $query->where('must_change_password', true);
    }

      /**
     * Get the student notes for this user (if they are a student).
     */
    public function studentNotes()
    {
        return $this->hasMany(StudentNote::class, 'student_id');
    }

    /**
     * Get the notes created by this user.
     */
    public function createdNotes()
    {
        return $this->hasMany(StudentNote::class, 'created_by');
    }

     public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }
}
