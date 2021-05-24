<!doctype html>
<html lang="{{ $locale }}">
<head>
    <meta charset="{{ $charset }}">
    <title></title>
</head>
<body>
<p>
    Dear {{ $name }},<br>
    <br>
    We've got a request to reset your password.<br>
    <br>
    Your password has been automatically reset to "<strong>{{ $password }}</strong>".<br>
    <br>
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━<br>
    This email is for sending only. Please do not make any reply to it.<br>
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
</p>
</body>
</html>
