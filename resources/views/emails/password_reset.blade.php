<!DOCTYPE html>
<html>
<head>
    <style>
        .button {
            background-color: #1e2d4d;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <h2>Hello!</h2>
    <p>You are receiving this email because we received a password reset request for your LexConnect account.</p>
    <p>Click the button below to reset your password:</p>
    <a href="{{ url('reset-password/'.$token.'?email='.$email) }}" class="button" style="color: white;">Reset Password</a>
    <p>This password reset link will expire in 60 minutes.</p>
    <p>If you did not request a password reset, no further action is required.</p>
    <p>Regards,<br>LexConnect Team</p>
</body>
</html>
