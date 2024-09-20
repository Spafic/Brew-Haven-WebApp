<?php
// 503.php
header("HTTP/1.1 503 Service Unavailable");
header("Status: 503 Service Unavailable");

// Retrieve the message from the URL parameter
$msg = isset($_GET['msg']) ? urldecode($_GET['msg']) : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Service Unavailable</title>
    <style>
        :root {
            --primary-color: #3a1f0d; /* Deep coffee brown */
            --secondary-color: #c7a17a; /* Warm beige */
            --accent-color: #e6b17e; /* Golden highlight */
            --text-color: #2c2c2c; /* Dark gray */
            --bg-color: #f9f3e9; /* Light cream */
            --white: #ffffff;
            --gray: #f4f4f4;
            --dark-gray: #4a4a4a;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Roboto', Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            text-align: center;
        }

        .container {
            background-color: var(--white);
            padding: 2em 3em;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            animation: fadeIn 1s ease-in;
        }

        h1 ,h4{
            font-size: 2.5em;
            margin-bottom: 0.7em;
            color: var(--primary-color);
            font-weight: bold;
        }

        p {
            font-size: 1.1em;
            margin-bottom: 1.5em !important;
            line-height: 1.6;
        }

        .message {
            margin: 0;
            color: var(--dark-gray);
        }

        .retry-button {
            display: inline-block;
            padding: 0.75em 1.5em;
            font-size: 1em;
            color: var(--white);
            background-color: var(--primary-color);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .retry-button:hover {
            background-color: var(--accent-color);
            transform: translateY(-2px);
        }

        .retry-button:active {
            background-color: var(--primary-color);
            transform: translateY(0);
        }

        .icons {
            margin-top: 1em;
        }

        .icons i {
            font-size: 2em;
            color: var(--primary-color);
            margin: 0 0.5em;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="../../assets/imgs/errors/error500.png" alt="Error 500">
        <h4> <i class="fas fa-mug-hot"></i> Internal Server Error</h4>
        <p class="message">We are currently performing maintenance on the site. Please try again later. We appreciate your patience.</p>
    </div>
</body>
</html>
