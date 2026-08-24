<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Assignment Notification</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f7;
            line-height: 1.6;
            color: #51545e;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f4f7;
            padding: 40px 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .email-header {
            background: linear-gradient(135deg, #FF5A1F 0%, #C63508 100%);
            padding: 40px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .email-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }
        .email-content {
            padding: 40px;
        }
        .email-content h2 {
            color: #333333;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .email-content p {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 15px;
            color: #51545e;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #FF5A1F;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 25px 0;
        }
        .detail-item {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
        }
        .detail-label {
            font-size: 12px;
            font-weight: 600;
            color: #8898aa;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .detail-value {
            font-size: 14px;
            color: #333333;
            font-weight: 500;
        }
        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .priority-high {
            background-color: #fee;
            color: #c33;
        }
        .priority-medium {
            background-color: #ffeaa7;
            color: #d63031;
        }
        .priority-low {
            background-color: #d4edda;
            color: #155724;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #FF5A1F 0%, #C63508 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 35px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(255, 90, 31, 0.4);
            margin: 20px 0;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 25px 40px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .email-footer p {
            font-size: 13px;
            color: #8898aa;
            margin: 5px 0;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                border-radius: 0 !important;
            }
            .email-header, .email-content, .email-footer {
                padding: 25px !important;
            }
            .details-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <h1>✅ New Task Assigned</h1>
                <p>You have been assigned a new task</p>
            </div>

            <div class="email-content">
                <h2>Hello, {{ $recipientName }}!</h2>

                <p>You have been assigned a new task. Please review the details below and complete it by the due date.</p>

                <div class="info-box">
                    <h3 style="margin: 0 0 10px 0; color: #FF5A1F; font-size: 18px;">{{ $taskTitle }}</h3>
                    @if($taskDescription)
                        <p style="margin: 0; font-size: 14px; color: #666;">{{ $taskDescription }}</p>
                    @endif
                </div>

                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">{{ ucfirst(str_replace('-', ' ', $taskStatus)) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Priority</div>
                        <div class="detail-value">
                            <span class="priority-badge priority-{{ $taskPriority }}">
                                {{ ucfirst($taskPriority) }}
                            </span>
                        </div>
                    </div>
                    @if($dueDate)
                    <div class="detail-item">
                        <div class="detail-label">Due Date</div>
                        <div class="detail-value">{{ $dueDate }}</div>
                    </div>
                    @endif
                    @if($estimatedHours)
                    <div class="detail-item">
                        <div class="detail-label">Estimated Hours</div>
                        <div class="detail-value">{{ $estimatedHours }}h</div>
                    </div>
                    @endif
                    @if($orderName)
                    <div class="detail-item">
                        <div class="detail-label">Order</div>
                        <div class="detail-value">{{ $orderName }}</div>
                    </div>
                    @endif
                    @if($teamName)
                    <div class="detail-item">
                        <div class="detail-label">Team</div>
                        <div class="detail-value">{{ $teamName }}</div>
                    </div>
                    @endif
                </div>

                @if($creatorName)
                <p style="font-size: 14px; color: #666;">
                    <strong>Assigned by:</strong> {{ $creatorName }}
                </p>
                @endif

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $taskUrl }}" class="btn">View Task Details</a>
                </div>

                <p style="font-size: 14px;">Please update the task status once you start working on it. If you have any questions, reach out to your team leader.</p>

                <p style="font-size: 14px;">Best regards,<br><strong>The TheSyndicate Team</strong></p>
            </div>

            <div class="email-footer">
                <p>&copy; {{ date('Y') }} TheSyndicate. All rights reserved.</p>
                <p>This email was sent to {{ $recipientName }}</p>
            </div>
        </div>
    </div>
</body>
</html>
