<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
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
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #4CAF50;
        }
        .content {
            padding: 20px 0;
        }
        .content h2 {
            color: #333;
        }
        .content p {
            line-height: 1.6;
            color: #666;
        }
        .button {
            text-align: center;
            margin: 20px 0;
        }
        .button a {
            background-color: #4CAF50;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        .footer {
            text-align: center;
            padding: 10px 0;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #e0e0e0;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Welcome!</h1>
    </div>
    <div class="content">
        <h2>Hello, {{ $data['first_name'] }} {{ $data['last_name'] }}</h2>
        <p>Your registration has been successfully completed.</p>
    </div>
    <div class="button">
        <a href="{{ env('APP_URL') }}">Visit Our Website</a>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
    </div>
</div>
</body>
</html>
