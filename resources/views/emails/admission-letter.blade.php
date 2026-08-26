<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letter of Admission - JBI University</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            line-height: 1.8;
            color: #333;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .letterhead {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-top: 5px solid #3b5bdb;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b5bdb;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #3b5bdb;
            margin-bottom: 5px;
        }
        .university-motto {
            font-style: italic;
            color: #666;
            font-size: 14px;
        }
        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            margin: 30px 0;
            text-transform: uppercase;
        }
        .details-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
        .signature-section {
            margin-top: 50px;
            text-align: right;
        }
        .stamp {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            border: 2px dashed #3b5bdb;
            color: #3b5bdb;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="letterhead">
        <div class="header" style="text-align: center;">
            <div style="margin-bottom: 12px;">
                @if(isset($message) && is_object($message))
                    <img src="{{ $message->embed(public_path('images/jbi-blue.webp')) }}" alt="JBI University Logo" style="max-height: 95px; width: auto;" />
                @else
                    <img src="{{ asset('images/jbi-blue.webp') }}" alt="JBI University Logo" style="max-height: 95px; width: auto;" />
                @endif
            </div>
            <h2 style="margin: 0 0 5px 0; color: #1e3a8a; font-size: 22px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">JBI UNIVERSITY</h2>
            <div class="university-motto" style="font-style: italic; color: #555; font-size: 13px; margin-bottom: 8px;">"Excellence in Education, Service to Humanity & Faith"</div>
            <p style="font-size: 11px; color: #666; margin-top: 5px; line-height: 1.5;">
                South Africa<br>
                WhatsApp: +27 68 443 8415 | Email: info@jbiuniversity.com | Website: www.jbiuniversity.com
            </p>
        </div>

        <p style="text-align: right;"><strong>Date:</strong> {{ now()->format('F j, Y') }}</p>
        <p style="text-align: right;"><strong>Ref No:</strong> JBI/ADM/{{ date('Y') }}/{{ str_pad($application->id, 4, '0', STR_PAD_LEFT) }}</p>

        <p style="margin-top: 30px;">
            <strong>{{ $application->full_name }}</strong><br>
            {{ $application->email }}<br>
            {{ $application->phone }}
        </p>

        <div class="title">LETTER OF ADMISSION</div>

        <p>Dear {{ $application->first_name }},</p>

        <p>On behalf of the Faculty, Administration, and Students of JBI University, I am delighted to inform you that you have been admitted to JBI University for the {{ date('Y') }}/{{ date('Y') + 1 }} Academic Year.</p>

        <div class="details-box">
            <h3 style="margin-top: 0;">Admission Details:</h3>
            <table style="width: 100%;">
                <tr>
                    <td><strong>Admission Number:</strong></td>
                    <td>{{ $application->admission_number }}</td>
                </tr>
                <tr>
                    <td><strong>Student Number:</strong></td>
                    <td>{{ $application->student_number }}</td>
                </tr>
                <tr>
                    <td><strong>Name:</strong></td>
                    <td>{{ $application->full_name }}</td>
                </tr>
                @if($application->type === 'student')
                <tr>
                    <td><strong>Program:</strong></td>
                    <td>{{ $application->program }}</td>
                </tr>
                @endif
                @if($application->type === 'faculty')
                <tr>
                    <td><strong>Position:</strong></td>
                    <td>{{ $application->position }}</td>
                </tr>
                <tr>
                    <td><strong>Department:</strong></td>
                    <td>{{ $application->department }}</td>
                </tr>
                @endif
                <tr>
                    <td><strong>Academic Year:</strong></td>
                    <td>{{ date('Y') }}/{{ date('Y') + 1 }}</td>
                </tr>
                <tr>
                    <td><strong>Admission Date:</strong></td>
                    <td>{{ $application->admitted_at->format('F j, Y') }}</td>
                </tr>
            </table>
        </div>

        <h3>Next Steps:</h3>
        <ol>
            <li><strong>Registration:</strong> Please complete your registration by {{ now()->addDays(14)->format('F j, Y') }}</li>
            <li><strong>Orientation:</strong> Attend the new student orientation on [Date to be announced]</li>
            <li><strong>Documents:</strong> Submit certified copies of your academic certificates and national ID</li>
            <li><strong>Medical Form:</strong> Complete the university medical examination form</li>
            <li><strong>Student ID:</strong> Visit the student affairs office to obtain your student ID card</li>
        </ol>

        <p><strong>Important Information:</strong></p>
        <ul>
            <li>Classes commence on {{ now()->addDays(30)->format('F j, Y') }}</li>
            <li>You must register before the deadline to secure your place</li>
            <li>Late registration may attract additional fees</li>
            <li>For any queries, contact: admission@jbiuniversity.com or WhatsApp +27 68 443 8415</li>
        </ul>

        <p>We congratulate you on your admission and look forward to welcoming you to JBI University. We are confident that you will make the most of the opportunities available to you and contribute positively to our academic community.</p>

        <p>Once again, congratulations and welcome to JBI University!</p>

        <div class="signature-section">
            <p><strong>Dr. ------ </strong><br>
            <em>Dean of Admissions</em><br>
            JBI University</p>
        </div>

        <div class="stamp">
            [OFFICIAL UNIVERSITY SEAL]
        </div>

        <div class="footer">
            <p><strong>This is an official admission letter from JBI University</strong></p>
            <p>&copy; {{ date('Y') }} JBI University. All rights reserved.</p>
            <p>Accredited by the South Africa National Council for Higher Education (SANCHE)</p>
        </div>
    </div>
</body>
</html>
