<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password Reset Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .header h1 {
            margin: 0;
            color: #FF5722;
        }

        .content {
            padding: 20px 0;
        }

        .content p {
            line-height: 1.6;
            color: #666;
            margin: 0 0 20px;
        }

        .button {
            text-align: center;
            margin: 20px 0;
        }

        .button a {
            background-color: #FF5722;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }

        .footer {
            text-align: center;
            padding: 20px 0;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #e0e0e0;
            margin-top: 20px;
        }

        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Password Reset Request</h1>
    </div>
    <div class="content">
        <h2>Hello, {{ $first_name }} {{ $last_name }}</h2>
        <p>We received a request to reset your password. Please click the button below to reset your password:</p>
        <div class="button">
            <a href="{{ env('FRONT_APP_URL') . '/reset-password?token=' . $token }}">Reset Password</a>
        </div>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
    </div>
</div>
</body>
</html>
