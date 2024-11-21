<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Роли</title>
</head>
<body>
<div class="forms-container">
    <h2>Index</h2>
    <form action="/api/ref/policy/role" method="GET">
        <input type="submit" value="Submit">
    </form>

    <h2>Store</h2>
    <form action="/api/ref/policy/role" method="POST">
        <label for="name">Name:</label><br>
        <input type="text" name="name"><br>
        <label for="description">Description:</label><br>
        <input type="text" name="description"><br>
        <label for="code">Code:</label><br>
        <input type="text" name="code"><br>
        <input type="submit" value="Submit">
    </form>

    <h2>Update</h2>
    <form action="/ref/policy/role/{id}" method="PUT">
        <label for="name">Name:</label><br>
        <input type="text" name="name"><br>
        <label for="description">Description:</label><br>
        <input type="text" name="description"><br>
        <label for="code">Code:</label><br>
        <input type="text" name="code"><br>
        <input type="submit" value="Submit">
    </form>

    <h2>Hard Delete</h2>
    <form action="/ref/policy/role/{id}" method="DELETE">
        <input type="submit" value="Submit">
    </form>

    <h2>Soft Delete</h2>
    <form action="/ref/policy/role/{id}/soft" method="DELETE">
        <input type="submit" value="Submit">
    </form>

    <h2>Restore</h2>
    <form action="/ref/policy/role/{id}/restore" method="POST">
        <input type="submit" value="Submit">
    </form>
</div>
</body>
</html>
