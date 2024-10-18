<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>
<body>
    <h1>Change Password</h1>
    <form action="{{ route('changePassword') }}" method="POST">

        <label for="oldPassword">Current Password</label>
        <input type="password" id="oldPassword" name="oldPassword" required><br><br>

        <label for="newPassword">New Password</label>
        <input type="password" id="newPassword" name="newPassword" required><br><br>
        <button type="submit">Confirm</button>
    </form>
</body>
</html>

