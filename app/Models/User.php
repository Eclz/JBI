<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'password',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'role',
        'role_id',
        'is_active',
        'email_verified_at',
        'must_change_password',
        'last_login_at',
        'profile_picture',
        'emergency_contact',
        'emergency_phone',
        'student_id',
        'employee_id',
        'preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'must_change_password' => 'boolean',
        'preferences' => 'array',
    ];

    /**
     * Get the student profile associated with the user.
     */
    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    /**
     * Get the faculty profile associated with the user.
     */
    public function facultyProfile(): HasOne
    {
        return $this->hasOne(FacultyProfile::class);
    }

    /**
     * Get the courses that the user is enrolled in (for students).
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_enrollments')
                    ->withPivot(['enrollment_date', 'status', 'notes', 'final_grade', 'letter_grade', 'grade_points', 'completion_date'])
                    ->withTimestamps();
    }

    /**
     * Get the course enrollments for the user (for students).
     */
    public function courseEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class, 'user_id');
    }

    /**
     * Get the courses that the user teaches (for faculty).
     */
    public function taughtCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Get the courses that the user teaches (for faculty).
     * Alias for taughtCourses to match old model
     */
    public function teachingCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Get the assignments submitted by the user.
     */
    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Get the grades for the user.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get the attendance records for the user.
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the fee records for the user.
     */
    public function feeRecords(): HasMany
    {
        return $this->hasMany(FeeRecord::class);
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get announcements created by user
     */
    public function createdAnnouncements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    /**
     * Get the forum topics created by the user.
     */
    public function forumTopics(): HasMany
    {
        return $this->hasMany(ForumTopic::class, 'created_by');
    }

    /**
     * Get the forum replies created by the user.
     */
    public function forumReplies(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'created_by');
    }

    /**
     * Get course materials uploaded by user
     */
    public function uploadedMaterials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class, 'uploaded_by');
    }

    /**
     * Get assignments graded by user (for faculty)
     */
    public function gradedAssignments(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'graded_by');
    }

    /**
     * Get grades given by user (for faculty)
     */
    public function givenGrades(): HasMany
    {
        return $this->hasMany(Grade::class, 'graded_by');
    }

    /**
     * Get attendance records marked by user (for faculty)
     */
    public function markedAttendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'marked_by');
    }

    /**
     * Get departments headed by user (for faculty/admin)
     */
    public function headedDepartments(): HasMany
    {
        return $this->hasMany(Department::class, 'head_of_department_id');
    }

    /**
     * Get fee records processed by user (for admin/finance)
     */
    public function processedFeeRecords(): HasMany
    {
        return $this->hasMany(FeeRecord::class, 'processed_by');
    }

    /**
     * Get the student notes for this user (if they are a student).
     */
    public function studentNotes(): HasMany
    {
        return $this->hasMany(StudentNote::class, 'student_id');
    }

    public function roleCatalog(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get the notes created by this user.
     */
    public function createdNotes(): HasMany
    {
        return $this->hasMany(StudentNote::class, 'created_by');
    }

    /**
     * User roles
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_FACULTY = 'faculty';
    const ROLE_STUDENT = 'student';
    const ROLE_PARENT = 'parent';

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role
            || $this->roleCatalog?->guard_role === $role
            || $this->roleCatalog?->slug === $role;
    }

    public function hasPermission(string $module, string $action): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->roleCatalog?->hasPermission($module, $action) ?? false;
    }

    public function getRoleNameAttribute(): string
    {
        return $this->roleCatalog?->name ?? ucfirst($this->role);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if user is faculty
     */
    public function isFaculty(): bool
    {
        return $this->hasRole(self::ROLE_FACULTY);
    }

    /**
     * Check if user is student
     */
    public function isStudent(): bool
    {
        return $this->hasRole(self::ROLE_STUDENT);
    }

    /**
     * Check if user is parent
     */
    public function isParent(): bool
    {
        return $this->hasRole(self::ROLE_PARENT);
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }
        return $this->name ?? '';
    }

    /**
     * Get initials attribute.
     */
    public function getInitialsAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
        }

        $nameParts = explode(' ', $this->name ?? '');
        if (count($nameParts) >= 2) {
            return strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
        }

        return strtoupper(substr($this->name ?? 'U', 0, 2));
    }

    /**
     * Get profile picture URL
     */
    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }

        $name = $this->full_name ?? $this->email;
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=1e3a8a&background=e0e7ff';
    }

    /**
     * Scope for filtering by role.
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
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
     * Scope for users by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Check if the student is admitted.
     */
    public function isAdmitted(): bool
    {
        return $this->role === self::ROLE_STUDENT && $this->studentProfile && $this->studentProfile->status === 'active';
    }

    /**
     * Generate default password for admission.
     */
    public static function generateJBIDefaultPassword(): string
    {
        return 'JBI@' . \Illuminate\Support\Str::random(8);
    }
}
