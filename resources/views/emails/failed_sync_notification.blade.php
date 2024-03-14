<!DOCTYPE html>
<html>
<head>
    <title>Failed Order Sync Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }
        h1 {
            color: #d9534f;
        }
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
<h1>Failed Order Sync Notification</h1>
<p>An error occurred while syncing orders from the WooCommerce API:</p>
<pre>{{ $error }}</pre>

<h2>Stack Trace</h2>
<pre>{{ $trace }}</pre>

<p>Please investigate the issue and take necessary actions to resolve it.</p>
<p>Thank you.</p>
</body>
</html>
