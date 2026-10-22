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
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, \Illuminate\Notifications\RoutesNotifications;

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
        'setup_email_sent_at' => 'datetime',
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
     * Get the HR employee profile associated with the user.
     */
    public function hrProfile(): HasOne
    {
        return $this->hasOne(HrEmployee::class);
    }

    /**
     * Get the latest application associated with the user (by email).
     */
    public function application(): HasOne
    {
        return $this->hasOne(Application::class, 'email', 'email')->latestOfMany();
    }

    /**
     * Get all applications associated with the user (by email).
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'email', 'email');
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
     * Get the total outstanding tuition fee balance for the student.
     */
    public function getTuitionBalance(): float
    {
        if (!$this->isStudent()) {
            return 0.0;
        }

        // Ensure student has invoices generated so an unbilled student is not falsely cleared
        \App\Services\FeeInvoiceService::ensureStudentInvoiced($this);

        $tuitionBalance = (float) FeeRecord::where('user_id', $this->id)
            ->where(function ($q) {
                $q->where('type', 'tuition')
                  ->orWhereHas('feeStructure', function ($fs) {
                      $fs->where('type', 'tuition');
                  });
            })
            ->where('status', '!=', 'paid')
            ->sum('balance_amount');

        return max(0.0, round($tuitionBalance, 2));
    }

    /**
     * Check if student has completed all tuition payments (zero tuition balance).
     */
    public function hasZeroTuitionBalance(): bool
    {
        return $this->getTuitionBalance() <= 0.001;
    }

    /**
     * Get total outstanding fee balance across all categories for the student.
     */
    public function getTotalFeeBalance(): float
    {
        if (!$this->isStudent()) {
            return 0.0;
        }

        \App\Services\FeeInvoiceService::ensureStudentInvoiced($this);

        $totalBalance = (float) FeeRecord::where('user_id', $this->id)
            ->where('status', '!=', 'paid')
            ->sum('balance_amount');

        return max(0.0, round($totalBalance, 2));
    }

    /**
     * Check whether student qualifies to sit for examinations.
     * Students who have not completed tuition do not qualify to sit for exams.
     */
    public function qualifiesForExams(): bool
    {
        return $this->hasZeroTuitionBalance();
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get unread notifications for this user.
     */
    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
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
     * Get faculties where this user is the appointed dean.
     */
    public function deanFaculties(): HasMany
    {
        return $this->hasMany(Faculty::class, 'dean_id');
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

    /**
     * Get the student ID or admission number.
     */
    public function getStudentIdAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        if ($this->relationLoaded('application')) {
            $appNumber = $this->application?->student_number ?: $this->application?->application_number;
            if (!empty($appNumber)) {
                return $appNumber;
            }
        }

        if ($this->relationLoaded('studentProfile')) {
            return $this->studentProfile?->admission_number ?: null;
        }

        $appNumber = $this->application()->value('student_number') ?: $this->application()->value('application_number');
        if (!empty($appNumber)) {
            return $appNumber;
        }

        return $this->studentProfile()->value('admission_number');
    }

    /**
     * Alias for student registration/student number.
     * Prioritizes student_id, then application student_number or application_number,
     * and falls back to student profile admission number.
     */
    public function getStudentNumberAttribute()
    {
        $rawStudentId = $this->attributes['student_id'] ?? null;
        if (!empty($rawStudentId)) {
            return $rawStudentId;
        }

        $app = $this->relationLoaded('application')
            ? $this->application
            : $this->application()->first();

        if ($app) {
            if (!empty($app->student_number)) {
                return $app->student_number;
            }
            if (!empty($app->application_number)) {
                return $app->application_number;
            }
        }

        $profile = $this->relationLoaded('studentProfile')
            ? $this->studentProfile
            : $this->studentProfile()->first();

        if ($profile && !empty($profile->admission_number)) {
            return $profile->admission_number;
        }

        return null;
    }

    /**
     * Get the admission number for this student.
     */
    public function getAdmissionNumberAttribute()
    {
        if ($this->relationLoaded('studentProfile')) {
            return $this->studentProfile?->admission_number;
        }

        if ($this->relationLoaded('application')) {
            return $this->application?->admission_number ?: $this->studentProfile()->value('admission_number');
        }

        return $this->studentProfile()->value('admission_number')
            ?: $this->application()->value('admission_number');
    }

    public function roleCatalog(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withPivot('is_primary')
            ->withTimestamps();
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
     * Check if user is Super Administrator (unrestricted system access)
     */
    public function isSuperAdmin(): bool
    {
        return $this->assignedRoles()->contains(
            fn (Role $role) => in_array($role->slug, ['super_administrator', 'super_admin'], true)
        );
    }

    /**
     * Check if user is an appointed Dean of any faculty or has Dean role.
     */
    public function isDean(): bool
    {
        return $this->hasAssignedRole(['dean'])
            || $this->deanFaculties()->exists();
    }

    /**
     * Check if user is an appointed Head of Department or has HOD role.
     */
    public function isHeadOfDepartment(): bool
    {
        return $this->hasAssignedRole(['head_of_department', 'hod'])
            || $this->headedDepartments()->exists();
    }

    /**
     * Check if user has a specific role or any role in a given list.
     */
    public function hasRole(string|array $role): bool
    {
        $rolesToCheck = is_array($role) ? $role : [$role];

        $assignedRoles = $this->assignedRoles();

        foreach ($rolesToCheck as $r) {
            if ($r === self::ROLE_ADMIN) {
                if ($assignedRoles->contains(
                    fn (Role $roleModel) => in_array($roleModel->slug, ['admin', 'super_administrator', 'super_admin'], true)
                )) {
                    return true;
                }

                continue;
            }

            if ($assignedRoles->contains(function (Role $roleModel) use ($r) {
                return $roleModel->slug === $r
                    || $roleModel->guard_role === $r
                    || strtolower($roleModel->name) === strtolower($r);
            })) {
                return true;
            }

            // 2. Appointed Dean
            if ($r === 'dean' && $this->isDean()) {
                return true;
            }

            // 3. Appointed Head of Department
            if (($r === 'head_of_department' || $r === 'hod') && $this->isHeadOfDepartment()) {
                return true;
            }

        }

        return false;
    }

    public function hasGuardRole(string $guardRole): bool
    {
        return $this->assignedRoles()->contains(
            fn (Role $roleModel) => $roleModel->guard_role === $guardRole
        );
    }

    public function getAssignedRoleIdentifiers(): array
    {
        return $this->assignedRoles()
            ->flatMap(fn (Role $role) => [$role->slug, $role->guard_role, strtolower($role->name)])
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Check if user has permission in any of their assigned roles.
     */
    public function hasPermission(string $module, string $action): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($this->assignedRoles() as $roleModel) {
            if ($roleModel->hasPermission($module, $action)) {
                return true;
            }
        }

        // Appointed deans inherit Dean role permissions
        if ($this->isDean()) {
            $deanRole = Role::where('slug', 'dean')->where('is_active', true)->first();
            if ($deanRole?->hasPermission($module, $action)) {
                return true;
            }
        }

        // Appointed department heads inherit Head of Department role permissions
        if ($this->isHeadOfDepartment()) {
            $hodRole = Role::where('slug', 'head_of_department')->where('is_active', true)->first();
            if ($hodRole?->hasPermission($module, $action)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(Role|string|int $role, bool $isPrimary = false): static
    {
        $roleModel = is_string($role)
            ? (Role::where('slug', $role)->where('is_active', true)->first()
                ?? Role::where('guard_role', $role)->where('is_active', true)->first())
            : (is_int($role) ? Role::find($role) : $role);

        if ($roleModel?->is_active) {
            $roleIds = $this->assignedRoles()->pluck('id')->map(fn ($id) => (int) $id)->all();
            if (!in_array((int) $roleModel->id, $roleIds, true)) {
                $roleIds[] = (int) $roleModel->id;
            }

            $primaryRoleId = $isPrimary || !$this->role_id
                ? (int) $roleModel->id
                : (int) $this->role_id;
            $this->syncRoles($roleIds, $primaryRoleId);
        }

        return $this;
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(Role|string|int $role): static
    {
        $roleModel = is_string($role)
            ? (Role::where('slug', $role)->first()
                ?? Role::where('guard_role', $role)->first())
            : (is_int($role) ? Role::find($role) : $role);

        if ($roleModel) {
            $this->roles()->detach($roleModel->id);
            $remainingRoleIds = $this->roles()
                ->where('roles.is_active', true)
                ->pluck('roles.id')
                ->map(fn ($id) => (int) $id)
                ->all();
            $primaryRoleId = in_array((int) $this->role_id, $remainingRoleIds, true)
                ? (int) $this->role_id
                : ($remainingRoleIds[0] ?? null);
            $this->syncRoles($remainingRoleIds, $primaryRoleId);
        }

        return $this;
    }

    /**
     * Synchronize multiple roles for the user.
     */
    public function syncRoles(array $roleIds, ?int $primaryRoleId = null): static
    {
        $roleIds = array_values(array_unique(array_map('intval', $roleIds)));
        $activeRoles = Role::whereIn('id', $roleIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');
        $roleIds = array_values(array_filter($roleIds, fn ($id) => $activeRoles->has($id)));

        if (!$primaryRoleId || !in_array((int) $primaryRoleId, $roleIds, true)) {
            $primaryRoleId = $roleIds[0] ?? null;
        }

        $syncData = [];
        foreach ($roleIds as $id) {
            $syncData[$id] = ['is_primary' => (int) $id === (int) $primaryRoleId];
        }

        $this->roles()->sync($syncData);
        $this->setPrimaryRole($primaryRoleId ? $activeRoles->get($primaryRoleId) : null);
        $this->unsetRelation('roles')->unsetRelation('roleCatalog');

        return $this;
    }

    /**
     * Return active roles that are explicitly attached to the user or set as primary.
     */
    private function assignedRoles(): EloquentCollection
    {
        $roles = $this->roles()->where('roles.is_active', true)->get();

        if ($this->role_id && !$roles->contains('id', $this->role_id)) {
            $primaryRole = Role::whereKey($this->role_id)->where('is_active', true)->first();
            if ($primaryRole) {
                $roles->push($primaryRole);
            }
        }

        return $roles->unique('id')->values();
    }

    private function hasAssignedRole(array $slugs): bool
    {
        return $this->assignedRoles()->contains(
            fn (Role $role) => in_array($role->slug, $slugs, true)
        );
    }

    private function setPrimaryRole(?Role $role): void
    {
        $this->forceFill([
            'role_id' => $role?->id,
            'role' => $role?->guard_role ?? self::ROLE_STUDENT,
        ])->save();
    }

    public function commissionMemberships(): HasMany
    {
        return $this->hasMany(ElectoralCommissionMember::class);
    }

    public function candidacies(): HasMany
    {
        return $this->hasMany(VotingCandidate::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function isElectoralCommissioner(?VotingSession $session = null): bool
    {
        if ($this->isAdmin() || $this->isSuperAdmin()) {
            return true;
        }

        if ($session) {
            return $this->commissionMemberships()
                ->where('voting_session_id', $session->id)
                ->where('is_active', true)
                ->exists();
        }

        return $this->commissionMemberships()->where('is_active', true)->exists();
    }

    public function getRoleNameAttribute(): string
    {
        $roleNames = $this->roles->pluck('name')->filter()->unique();
        if ($roleNames->isNotEmpty()) {
            return $roleNames->join(' / ');
        }
        return $this->roleCatalog?->name ?? ucfirst($this->role);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasAssignedRole(['admin', 'super_administrator', 'super_admin']);
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
     * Check if user is HR staff
     */
    public function isHrStaff(): bool
    {
        return $this->hasRole('hr') || $this->hasRole('human_resources') || $this->hasPermission('human_resources', 'view');
    }

    /**
     * Check if user is Finance Officer
     */
    public function isFinanceOfficer(): bool
    {
        return $this->hasRole('finance_officer') || $this->hasRole('finance') || $this->hasPermission('finance_hub', 'view');
    }

    /**
     * Check if user is Facilities staff
     */
    public function isFacilitiesStaff(): bool
    {
        return $this->hasRole('facilities') || $this->hasRole('facilities_staff') || $this->hasPermission('facilities', 'view');
    }

    /**
     * Check if user is Librarian
     */
    public function isLibrarian(): bool
    {
        return $this->hasRole('librarian') || $this->hasRole('library_staff') || $this->hasPermission('library', 'view');
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
     * Get faculty ID for student
     */
    public function getFacultyId(): ?int
    {
        return $this->studentProfile?->department?->faculty_id
            ?? $this->studentProfile?->program?->department?->faculty_id
            ?? $this->application?->programRecord?->department?->faculty_id
            ?? null;
    }

    /**
     * Get faculty name for student
     */
    public function getFacultyName(): ?string
    {
        return $this->studentProfile?->department?->faculty?->name
            ?? $this->studentProfile?->program?->department?->faculty?->name
            ?? $this->application?->programRecord?->department?->faculty?->name
            ?? null;
    }

    /**
     * Scope for filtering by role.
     */
    public function scopeRole($query, $role)
    {
        $roles = is_array($role) ? $role : [$role];

        return $query->where(function ($userQuery) use ($roles) {
            foreach ($roles as $roleName) {
                $matchesRole = function ($roleQuery) use ($roleName) {
                    $roleQuery->where('is_active', true)
                        ->where(function ($query) use ($roleName) {
                            if ($roleName === self::ROLE_ADMIN) {
                                $query->whereIn('slug', ['admin', 'super_administrator', 'super_admin'])
                                    ->orWhereRaw('LOWER(name) = ?', [strtolower($roleName)]);
                            } else {
                                $query->where('slug', $roleName)
                                    ->orWhere('guard_role', $roleName)
                                    ->orWhereRaw('LOWER(name) = ?', [strtolower($roleName)]);
                            }
                        });
                };

                $userQuery->orWhereHas('roles', $matchesRole)
                    ->orWhereHas('roleCatalog', $matchesRole);
            }
        });
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
        return $this->isStudent() && $this->studentProfile && $this->studentProfile->status === 'active';
    }

    /**
     * Generate default password for admission.
     */
    public static function generateJBIDefaultPassword(): string
    {
        return 'JBI@' . \Illuminate\Support\Str::random(8);
    }

    /**
     * Send password reset notification with custom branded template.
     */
    public function sendPasswordResetNotification($token): void
    {
        try {
            \Illuminate\Support\Facades\Mail::to($this->email)->send(new \App\Mail\ResetPasswordMail($this, $token));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send password reset email: ' . $e->getMessage());
        }
    }
}
