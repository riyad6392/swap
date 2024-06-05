<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swap Initiated</title>
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

        h2 {
            color: #4CAF50;
            margin-top: 0;
        }

        p {
            line-height: 1.6;
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
    <h2>Hello {{ $data['exchanged_user_name'] }},</h2>
    <p>You have received a swap request from {{ $data['requested_user_name']}}.</p>
    <p>Please log in to your account to view and respond to the swap request.</p>
    <p>Thank you,</p>
    <p>Your Company</p>

    <div class="footer">
        <p>This is an automated email. Please do not reply.</p>
    </div>
</div>

</body>
</html>
