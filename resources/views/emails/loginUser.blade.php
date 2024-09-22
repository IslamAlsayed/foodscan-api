<!DOCTYPE html>
<html>

<head>
    <title>Welcome Back to foodScan!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: 0 auto;
            padding: 20px;
            max-width: 600px;
            background-color: #ffffff;
            border: 1px solid #ddd;
        }

        .header {
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        .header.logged.in {
            background-color: #4CAF50;
        }

        .header.logged.out {
            background-color: #e12929;
        }

        .header.register {
            background-color: #3548dc;
        }

        .content {
            padding: 20px;
        }

        .content p {
            margin: 10px 0;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: #777;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }

        .button.logged.in {
            background-color: #4CAF50;
        }

        .button.logged.out {
            background-color: #e12929;
        }

        .button.register {
            background-color: #3548dc;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header {{$operate}}">
            <h2>Welcome Back to foodScan!</h2>
        </div>
        <div class="content">
            @if($operate == 'logged in')
            <p>Dear Valued {{ ucfirst($user->name) }},</p>
            <p>Thank you for logging back into foodScan! We’re thrilled to have you with us.</p>
            <p>If you have any questions or need assistance, feel free to reach out to our support team.</p>
            <p>Happy exploring!</p>
            <a href="#" class="button {{$operate}}">Visit my site</a>
            @elseif($operate == 'logged out')
            <p>Dear {{ ucfirst($user->name) }},</p>
            <p>You have successfully logged out of your foodScan account.</p>
            <p>We're sorry to see you go! If you have any feedback or questions, please reach out to us.</p>
            <p>We hope to see you back soon!</p>
            <a href="#" class="button register">Visit our site</a>
            @elseif($operate == 'register')
            <p>Dear {{ ucfirst($user->name) }},</p>
            <p>Thank you for registering with foodScan! We’re excited to have you on board.</p>
            <p>Your journey starts now, and we can’t wait for you to explore our services.</p>
            <p>If you need any assistance, our support team is always here to help!</p>
            <a href="#" class="button register">Get Started</a>
            @endif
        </div>
        <div class="footer">
            <p>&copy; 2024 foodScan. All rights reserved.</p>
        </div>
    </div>
</body>

</html>