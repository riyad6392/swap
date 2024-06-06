<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Request Notification</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }

        h2 {
            color: #4CAF50;
            margin-top: 5px;
        }

        p {
            line-height: 1.6;
            margin: 10px 0;
            color: #555;
        }

        .footer {
            text-align: center;
            padding: 10px 0;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #e0e0e0;
            margin-top: 20px;
        }

        .button {
            display: inline-block;
            background-color: #3498db;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #2980b9;
        }

        .header {
            background-color: #3498db;
            color: #ffffff;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .details p {
            margin: 5px 0;
        }

        .details strong {
            color: #333;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Admin Request Notification</h1>
    </div>
    <h2>Hello {{ $data['received_user_name'] }},</h2>
    <p>You have received an admin request from <strong>{{ $data['requested_admin_first_name'] }} {{ $data['requested_admin_last_name'] }}</strong>.</p>
    <div class="details">
        <p><strong>Role Name:</strong> {{$data['role']}}</p>
        <p><strong>Email:</strong> {{$data['email']}}</p>
        <p><strong>Password:</strong> {{$data['password']}}</p>
    </div>
    <p>Please log in to your account to view and respond to the admin request.</p>
    <a href="https://www.swap.com/login" class="button">Log In</a>
    <p>Thank you,</p>
    <p>Your Swap</p>

    <div class="footer">
        <p>This is an automated email. Please do not reply.</p>
    </div>
</div>

</body>
</html>
