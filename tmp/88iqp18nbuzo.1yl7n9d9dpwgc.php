<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
</head>
<body>
<h1>Update your user profile here.</h1>
<hr>
<form method="post" action="/show">
    <label>Email</label>
    <input type="email" name="email" value="<?= ($user) ?>" readonly><br><hr>
    <label>Name</label>
    <input type="text" name="name" required><br><hr>
    <label>Phone Number</label>
    <input type="text" name="phone" required><br><hr>
    <input type="submit" value="update">
</form>
</body>
</html>