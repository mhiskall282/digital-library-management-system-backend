<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'UEW Digital Library Notification' }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 0; background-color: #F8FAFC; color: #1E293B; line-height: 1.6; }
        .wrapper { max-width: 600px; margin: 30px auto; background-color: #FFFFFF; border-radius: 16px; overflow: hidden; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .header { background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 100%); padding: 32px 24px; text-align: center; border-bottom: 4px solid #C41E3A; }
        .header h1 { color: #FFFFFF; font-size: 20px; font-weight: 800; margin: 0; letter-spacing: -0.5px; }
        .header p { color: #93C5FD; font-size: 12px; font-weight: 600; text-transform: uppercase; margin: 6px 0 0; letter-spacing: 1px; }
        .content { padding: 32px 28px; }
        .btn { display: inline-block; background-color: #C41E3A; color: #FFFFFF !important; text-decoration: none; padding: 12px 28px; font-size: 13px; font-weight: 700; border-radius: 10px; margin: 20px 0; text-align: center; }
        .badge { display: inline-block; background-color: #EFF6FF; color: #1E3A8A; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; }
        .footer { background-color: #F8FAFC; padding: 24px; text-align: center; border-top: 1px solid #E2E8F0; font-size: 11px; color: #64748B; }
        .credential-box { background-color: #F1F5F9; border: 1px dashed #CBD5E1; border-radius: 10px; padding: 16px; margin: 18px 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>UEW DIGITAL LIBRARY</h1>
            <p>School of Business &middot; Academic Repository</p>
        </div>

        <div class="content">
            @yield('content')
        </div>

        <div class="footer">
            <p style="margin: 0 0 6px;"><strong>University of Education, Winneba &mdash; School of Business</strong></p>
            <p style="margin: 0 0 6px;">North Campus, Winneba, Ghana &middot; <a href="mailto:library@uew.edu.gh" style="color: #C41E3A;">library@uew.edu.gh</a></p>
            <p style="margin: 8px 0 0; font-size: 10px; color: #94A3B8;">This is an automated system dispatch from the UEW Digital Library platform. Please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html>
