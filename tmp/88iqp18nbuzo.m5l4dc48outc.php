<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Show Result</title>
</head>
<body>
<table>
    <tr>
        <td>User ID</td>
        <td>First Name</td>
        <td>Last Name</td>
<!--        <td>Profile URL</td>-->
    </tr>
    <?php foreach (($result?:[]) as $item): ?>
        <tr>
            <td><?= ($item['user_id']) ?></td>
            <td><?= ($item['first_name']) ?></td>
            <td><?= ($item['last_name']) ?></td>
<!--            <td><a href="/user/<?= ($item['id']) ?>"></a></td>-->
        </tr>
    <?php endforeach; ?>

</table>
</body>
</html>