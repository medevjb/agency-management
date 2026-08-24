<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to TheSyndicate</title>
    <style>
        /* Reset styles */
        body, p, h1, h2, h3, h4, h5, h6 {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f4f7;
            -webkit-font-smoothing: antialiased;
            line-height: 1.6;
            color: #51545e;
        }

        /* Responsive wrapper */
        .email-wrapper {
            width: 100%;
            background-color: #f4f4f7;
            padding: 40px 0;
        }

        /* Email container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #FF5A1F 0%, #C63508 100%);
            padding: 40px;
            text-align: center;
        }

        .email-header h1 {
            color: #ffffff;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .email-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }

        /* Content */
        .email-content {
            padding: 40px;
        }

        .email-content h2 {
            color: #333333;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .email-content p {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 20px;
            color: #51545e;
        }

        .welcome-text {
            background-color: #f8f9fa;
            border-left: 4px solid #FF5A1F;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }

        /* Credentials box */
        .credentials-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .credentials-box h3 {
            color: #FF5A1F;
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .credential-item:last-child {
            border-bottom: none;
        }

        .credential-label {
            font-weight: 600;
            color: #333333;
            font-size: 14px;
        }

        .credential-value {
            color: #51545e;
            font-size: 14px;
            font-weight: 500;
        }

        /* Button */
        .btn-container {
            text-align: center;
            margin: 40px 0;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #FF5A1F 0%, #C63508 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(255, 90, 31, 0.4);
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 90, 31, 0.6);
        }

        /* Info cards */
        .info-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 30px 0;
        }

        .info-card {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .info-card-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .info-card-title {
            font-weight: 600;
            color: #333333;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .info-card-value {
            color: #FF5A1F;
            font-weight: 600;
            font-size: 16px;
        }

        /* Footer */
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .email-footer p {
            font-size: 14px;
            color: #8898aa;
            margin-bottom: 10px;
        }

        .email-footer a {
            color: #FF5A1F;
            text-decoration: none;
        }

        .social-links {
            margin-top: 20px;
        }

        .social-links a {
            display: inline-block;
            margin: 0 10px;
            font-size: 20px;
            color: #8898aa;
            text-decoration: none;
        }

        /* Warning box */
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .warning-box p {
            font-size: 14px;
            color: #856404;
            margin: 0;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                border-radius: 0 !important;
            }

            .email-header, .email-content, .email-footer {
                padding: 25px !important;
            }

            .info-cards {
                grid-template-columns: 1fr !important;
            }

            .credential-item {
                flex-direction: column;
                gap: 5px;
            }

            .btn {
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <h1>Welcome to TheSyndicate!</h1>
                <p>We're thrilled to have you on board</p>
            </div>

            <!-- Content -->
            <div class="email-content">
                <h2>Hello, {{ $userName }}!</h2>

                <div class="welcome-text">
                    <p>Welcome to TheSyndicate team! Your account has been successfully created and you're now ready to collaborate with your team members.</p>
                </div>

                <p>We've set up your account with the following details:</p>

                <!-- Credentials Box -->
                <div class="credentials-box">
                    <h3>🔐 Your Login Credentials</h3>
                    <div class="credential-item">
                        <span class="credential-label">Email:</span>
                        <span class="credential-value">{{ $userEmail }}</span>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Password:</span>
                        <span class="credential-value">{{ $userPassword }}</span>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Employee ID:</span>
                        <span class="credential-value">{{ $employeeId }}</span>
                    </div>
                </div>

                <div class="warning-box">
                    <p>⚠️ <strong>Important:</strong> For your security, please change your password after your first login.</p>
                </div>

                <!-- Info Cards -->
                <div class="info-cards">
                    <div class="info-card">
                        <div class="info-card-icon">👤</div>
                        <div class="info-card-title">Your Role</div>
                        <div class="info-card-value">{{ ucfirst($userRole) }}</div>
                    </div>
                    @if($position)
                    <div class="info-card">
                        <div class="info-card-icon">💼</div>
                        <div class="info-card-title">Position</div>
                        <div class="info-card-value">{{ $position }}</div>
                    </div>
                    @endif
                    @if($team)
                    <div class="info-card">
                        <div class="info-card-icon">👥</div>
                        <div class="info-card-title">Team</div>
                        <div class="info-card-value">{{ $team }}</div>
                    </div>
                    @endif
                    <div class="info-card">
                        <div class="info-card-icon">🚀</div>
                        <div class="info-card-title">Status</div>
                        <div class="info-card-value">Active</div>
                    </div>
                </div>

                <p>Click the button below to access your dashboard and get started:</p>

                <!-- CTA Button -->
                <div class="btn-container">
                    <a href="{{ $loginUrl }}" class="btn">Access Your Dashboard</a>
                </div>

                <p>If you have any questions or need assistance, feel free to reach out to our support team. We're here to help you succeed!</p>

                <p>Best regards,<br><strong>The TheSyndicate Team</strong></p>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} TheSyndicate. All rights reserved.</p>
                <p>This email was sent to {{ $userEmail }}</p>
                <div class="social-links">
                    <a href="#">🌐</a>
                    <a href="#">📧</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
