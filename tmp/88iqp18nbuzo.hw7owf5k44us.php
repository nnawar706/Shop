<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
</head>
<body>
<h1>Profile Information</h1>
<hr>
<img src="<?= ($profileData['image_url']) ?>" height="40px" width="40px"><hr>
<p>Name: <b><?= ($profileData['name']) ?></b></p><hr>
<p>Email: <b><?= ($profileData['email']) ?></b></p><hr>
<p>Phone no.: <b><?= ($profileData['phone']) ?></b></p><hr>
</body>
</html>