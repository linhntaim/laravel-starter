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
    Please click the link below to verify your email.<br>
    <br>
    ----------------------------------------------------------------<br>
    URL: <a href="{{ $url_verify_email }}">{{ $url_verify_email }}</a><br>
    @if ($expired_at)
        The link will be expired at: {{ $expired_at }}<br>
    @endif
    ----------------------------------------------------------------<br>
    <br>
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━<br>
    This email is for sending only. Please do not make any reply to it.<br>
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
</p>
</body>
</html>
