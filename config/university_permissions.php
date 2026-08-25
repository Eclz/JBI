<?php

return [
    'actions' => [
        'view' => 'View',
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'approve' => 'Approve',
        'export' => 'Export',
    ],

    'modules' => [
        'users' => 'User Management',
        'roles' => 'Roles & Permissions',
        'students' => 'Student Records',
        'faculty' => 'Faculty & Staff',
        'departments' => 'Departments',
        'programs' => 'Programs',
        'courses' => 'Courses',
        'enrollments' => 'Enrollments',
        'applications' => 'Admissions Applications',
        'fees' => 'Fees & Payments',
        'attendance' => 'Attendance',
        'grades' => 'Grades',
        'lms' => 'Learning Materials',
        'exams' => 'Exams & Quizzes',
        'reports' => 'Reports',
        'settings' => 'System Settings',
        'evoting' => 'E-Voting & Student Leadership',
        'finance_hub' => 'Finance & Bursar Hub',
        'revenue' => 'Revenue & Income',
        'budgets' => 'Department Budgets',
        'expenses' => 'Expenditures',
        'payables' => 'Accounts Payable',
        'receivables' => 'Accounts Receivable',
        'payroll' => 'Payroll Management',
        'assets' => 'Asset Management',
        'banking' => 'Banking & Cash',
        'financial_statements' => 'Financial Statements',
    ],

    'defaults' => [
        'super_administrator' => [
            'name' => 'Super Administrator',
            'guard_role' => 'admin',
            'description' => 'Full system access for institutional administrators.',
        ],
        'registrar' => [
            'name' => 'Registrar',
            'guard_role' => 'admin',
            'description' => 'Manages student records, enrollments, programs, and academic reporting.',
        ],
        'dean' => [
            'name' => 'Dean',
            'guard_role' => 'faculty',
            'description' => 'Oversees faculty, departments, programs, and academic performance.',
        ],
        'head_of_department' => [
            'name' => 'Head of Department',
            'guard_role' => 'faculty',
            'description' => 'Manages department courses, lecturers, attendance, and grades.',
        ],
        'lecturer' => [
            'name' => 'Lecturer',
            'guard_role' => 'faculty',
            'description' => 'Manages assigned courses, materials, attendance, assignments, exams, and grades.',
        ],
        'finance_officer' => [
            'name' => 'Finance Officer',
            'guard_role' => 'admin',
            'description' => 'Manages fee structures, payments, receipts, and financial reports.',
        ],
        'admissions_officer' => [
            'name' => 'Admissions Officer',
            'guard_role' => 'admin',
            'description' => 'Reviews applications and admission payment verification.',
        ],
        'student' => [
            'name' => 'Student',
            'guard_role' => 'student',
            'description' => 'Access to personal courses, fees, attendance, grades, LMS, and exams.',
        ],
        'parent_guardian' => [
            'name' => 'Parent / Guardian',
            'guard_role' => 'parent',
            'description' => 'Read-only guardian access to student progress and finance information.',
        ],
    ],
];
