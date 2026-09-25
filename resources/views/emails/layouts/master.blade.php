<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name', 'JBI University'))</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td, a { font-family: Arial, Helvetica, sans-serif !important; }
    </style>
    <![endif]-->
    <style>
        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6fb;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            color: #2d3748;
            line-height: 1.6;
        }
        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            border: 0;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }
        a {
            color: #3b5bdb;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }

        /* Container */
        .email-wrapper {
            width: 100%;
            background-color: #f4f6fb;
            padding: 40px 15px;
        }
        .email-card {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #1a2236 0%, #293b61 50%, #3b5bdb 100%);
            padding: 32px 30px;
            text-align: center;
            color: #ffffff;
        }
        .brand-title {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0;
            color: #ffffff;
            text-transform: uppercase;
        }
        .brand-subtitle {
            font-size: 13px;
            color: #cbd5e1;
            margin: 4px 0 0 0;
            letter-spacing: 0.3px;
        }

        /* Content Body */
        .email-body {
            padding: 35px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1a202c;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .content-box {
            font-size: 15px;
            color: #4a5568;
            line-height: 1.7;
        }

        /* Alert / Callout Box */
        .callout {
            background-color: #f8fafc;
            border-left: 4px solid #3b5bdb;
            border-radius: 6px;
            padding: 16px 20px;
            margin: 24px 0;
        }
        .callout-urgent {
            background-color: #fef2f2;
            border-left-color: #ef4444;
        }
        .callout-warning {
            background-color: #fffbeb;
            border-left-color: #f59e0b;
        }
        .callout-success {
            background-color: #f0fdf4;
            border-left-color: #10b981;
        }

        /* Badges */
        .badge {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .badge-primary { background-color: #e0e7ff; color: #3b5bdb; }
        .badge-urgent { background-color: #fee2e2; color: #dc2626; }
        .badge-high { background-color: #fef3c7; color: #d97706; }
        .badge-success { background-color: #d1fae5; color: #059669; }
        .badge-info { background-color: #e0f2fe; color: #0284c7; }

        /* Buttons */
        .button-wrapper {
            text-align: center;
            margin: 30px 0 20px 0;
        }
        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #3b5bdb 0%, #2f49b5 100%);
            color: #ffffff !important;
            font-weight: 600;
            font-size: 15px;
            padding: 12px 32px;
            border-radius: 8px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(59, 91, 219, 0.3);
            transition: all 0.2s ease;
        }

        /* Details / Meta info */
        .meta-table {
            width: 100%;
            margin: 20px 0;
            background-color: #f8fafc;
            border-radius: 8px;
            border: 1px solid #edf2f7;
        }
        .meta-table td {
            padding: 10px 16px;
            font-size: 13px;
            border-bottom: 1px solid #edf2f7;
        }
        .meta-table td:first-child {
            font-weight: 600;
            color: #64748b;
            width: 35%;
        }
        .meta-table td:last-child {
            color: #1e293b;
        }
        .meta-table tr:last-child td {
            border-bottom: none;
        }

        /* Sign-off */
        .signoff {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #edf2f7;
            font-size: 14px;
            color: #64748b;
        }
        .signoff strong {
            color: #1a202c;
        }

        /* Footer */
        .email-footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.5;
        }
        .email-footer a {
            color: #64748b;
            text-decoration: underline;
        }
        .footer-links {
            margin-bottom: 12px;
        }
        .footer-links a {
            margin: 0 8px;
            color: #3b5bdb;
            text-decoration: none;
            font-weight: 500;
        }

        /* Mobile */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 15px 5px !important;
            }
            .email-card {
                border-radius: 8px !important;
            }
            .email-header {
                padding: 24px 20px !important;
            }
            .email-body {
                padding: 24px 20px !important;
            }
            .brand-title {
                font-size: 20px !important;
            }
            .btn-primary {
                display: block !important;
                padding: 14px 20px !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <table role="presentation" class="email-wrapper" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <div class="email-card">
                    <!-- Brand Header -->
                    <div class="email-header">
                        <div class="brand-title">{{ config('app.name', 'JBI University') }}</div>
                        <div class="brand-subtitle">Academic & Institutional Portal</div>
                        @yield('header_badge')
                    </div>

                    <!-- Email Main Content -->
                    <div class="email-body">
                        @yield('content')

                        <!-- System Sign-off -->
                        @section('signoff')
                        <div class="signoff">
                            <p style="margin: 0 0 4px 0;">Best regards,</p>
                            <p style="margin: 0;"><strong>{{ config('app.name', 'JBI University') }} Administration & Academic Services</strong></p>
                        </div>
                        @show
                    </div>

                    <!-- Footer -->
                    <div class="email-footer">
                        <div class="footer-links">
                            <a href="{{ config('app.url', url('/')) }}/login">Portal Login</a> &bull;
                            <a href="{{ config('app.url', url('/')) }}/dashboard">Dashboard</a> &bull;
                            <a href="{{ config('app.url', url('/')) }}/support">Help Desk</a>
                        </div>
                        <p style="margin: 0 0 6px 0;">&copy; {{ date('Y') }} {{ config('app.name', 'JBI University') }}. All rights reserved.</p>
                        <p style="margin: 0;">This is an automated notification from the {{ config('app.name', 'JBI University') }} Management System dispatched to active university accounts (Students, Faculty, and Staff).</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
