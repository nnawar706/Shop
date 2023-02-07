<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form with Validation</title>
    <link rel="stylesheet" href="../../ui/css/style.css"/>
</head>
<body>
<div class="card">
    <h2>Form</h2>
    <form class="row" action="/save_user" enctype="multipart/form-data" method="post">
        <div class="col">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name">
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email">
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone">
            </div>
        </div>
        <a href="purchase.html"></a>

<!--        <div class="col">-->
<!--            <div class="form-group">-->
<!--                <label>quantity</label>-->
<!--                <input type="text" name="quantity" id="quantity">-->
<!--            </div>-->
<!--        </div>-->

<!--        <div class="col">-->
<!--            <div class="form-group">-->
<!--                <label>Total</label>-->
<!--                <input type="text" name="total" id="total" readonly>-->
<!--            </div>-->
<!--        </div>-->

<!--        <div class="col">-->
<!--            <div class="form-group">-->
<!--                <label>Promo Code</label>-->
<!--                <input type="password" name="code">-->
<!--            </div>-->
<!--        </div>-->

        <div class="col">
            <input type="submit" value="Submit">
        </div>
    </form>
</div>
<script type="text/javascript" src="../../ui/js/main.js"></script>
</body>
</html>