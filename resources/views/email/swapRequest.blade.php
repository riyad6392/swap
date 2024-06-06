<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swap Initiated</title>
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
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Swap Request Notification</h1>
    </div>
    <h2>Hello {{ $data['exchanged_user_first_name'] }} {{ $data['exchanged_user_last_name'] }},</h2>
    <p>You have received a swap request from <strong>{{ $data['requested_user_first_name']}} {{ $data['requested_user_last_name']}}</strong>.</p>
    <p>Please log in to your account to view and respond to the swap request.</p>
    <a href="https://www.swap.com/login" class="button">Log In</a>
    <p>Thank you,</p>
    <p>SWAP</p>

    <div class="footer">
        <p>This is an automated email. Please do not reply.</p>
    </div>
</div>

</body>
</html>
