<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
</head>
<body>
<form action="RegisterController.php" method="post">
    <label for="email"></label>
    <input type="text" name="email" id="email">
    <label for="password"></label>
    <input type="password" name="password" id="password">
    <label for="confirmPassword"></label>
    <input type="password" name="confirmPassword" id="confirmPassword">
    <button type="submit">Sign Up</button>
</form>

</body>
</html>
