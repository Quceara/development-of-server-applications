<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
</head>
<body>
    <form action="{{ route('logout') }}" method="POST">
        <button type="submit">Logout</button>
    </form>

    <form action="{{ route('logoutAll') }}" method="POST" style="margin-top: 20px;">
        <button type="submit">Logout All Sessions</button>
    </form>
</body>
</html>

