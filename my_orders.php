<?php
include "db.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);

$orders = mysqli_query($conn, "
    SELECT *
    FROM orders
    WHERE user_id = $user_id
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>My Orders - FoodExpress</title>

<link rel="stylesheet"
href="css/style.css">

<style>

/* =========================
   MY ORDERS
========================= */

.orders-section {
    min-height: 70vh;
    padding: 45px 7%;
    background: #f7f7f7;
}

.orders-header {
    max-width: 1000px;
    margin: 0 auto 30px;
}

.orders-header h1 {
    font-size: 32px;
    margin-bottom: 5px;
}

.orders-header p {
    color: #777;
}


/* ORDER LIST */

.orders-list {
    max-width: 1000px;
    margin: auto;
    display: flex;
    flex-direction: column;
    gap: 18px;
}


/* ORDER CARD */

.order-card {
    background: white;
    border-radius: 15px;
    padding: 23px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.order-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 22px rgba(0,0,0,0.11);
}


/* TOP */

.order-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
}

.order-id {
    font-size: 20px;
    font-weight: bold;
}

.order-date {
    color: #777;
    font-size: 14px;
    margin-top: 4px;
}


/* DETAILS */

.order-details {
    display: grid;
    grid-template-columns:
        repeat(3, 1fr);

    gap: 15px;
    padding: 20px 0;
}

.detail-box {
    background: #f8f8f8;
    padding: 13px;
    border-radius: 9px;
}

.detail-label {
    display: block;
    color: #777;
    font-size: 13px;
    margin-bottom: 5px;
}

.detail-value {
    font-weight: bold;
}


/* STATUS */

.status {
    display: inline-block;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 13px;
}


/* STATUS COLORS */

.placed {
    background: #fff3cd;
    color: #856404;
}

.confirmed {
    background: #cfe2ff;
    color: #084298;
}

.preparing {
    background: #ffe5d0;
    color: #984c0c;
}

.delivery {
    background: #cff4fc;
    color: #055160;
}

.delivered {
    background: #d1e7dd;
    color: #0f5132;
}

.cancelled {
    background: #f8d7da;
    color: #842029;
}


/* BOTTOM */

.order-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    border-top: 1px solid #eee;
    padding-top: 17px;
}

.order-total {
    font-size: 20px;
    font-weight: bold;
    color: #e63946;
}

.order-button {
    display: inline-block;
    padding: 11px 20px;
    background: #e63946;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: 0.3s;
}

.order-button:hover {
    background: #c92f3b;
}


/* EMPTY */

.empty-orders {
    max-width: 600px;
    margin: 50px auto;
    background: white;
    padding: 50px 25px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.empty-orders h2 {
    margin-bottom: 10px;
}

.empty-orders p {
    color: #777;
    margin-bottom: 25px;
}

.shop-button {
    display: inline-block;
    padding: 12px 25px;
    background: #e63946;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
}


/* MOBILE */

@media (max-width: 700px) {

    .orders-section {
        padding: 35px 5%;
    }

    .order-top {
        flex-direction: column;
        align-items: flex-start;
    }

    .order-details {
        grid-template-columns: 1fr;
    }

    .order-bottom {
        flex-direction: column;
        align-items: stretch;
    }

    .order-button {
        text-align: center;
    }

}

</style>

</head>

<body>


<!-- NAVBAR -->

<nav class="navbar">

<div class="logo">
FoodExpress
</div>

<div class="nav-links">

<a href="index.php">
Home
</a>

<a href="index.php#foods">
Menu
</a>

<a href="my_orders.php">
My Orders
</a>

<a href="cart.php">
Cart
</a>

</div>

</nav>


<!-- ORDERS -->

<section class="orders-section">

<div class="orders-header">

<h1>
My Orders
</h1>

<p>
Track your previous and current food orders.
</p>

</div>


<?php if (mysqli_num_rows($orders) == 0) { ?>


<div class="empty-orders">

<h2>
No Orders Yet
</h2>

<p>
You haven't placed any food orders yet.
</p>

<a
href="index.php"
class="shop-button"
>
Start Ordering
</a>

</div>


<?php } else { ?>


<div class="orders-list">

<?php while ($order = mysqli_fetch_assoc($orders)) { ?>


<?php

$status = $order['order_status'];

$status_class = 'placed';

if ($status == 'Confirmed') {
    $status_class = 'confirmed';
}

elseif ($status == 'Preparing') {
    $status_class = 'preparing';
}

elseif ($status == 'Out for Delivery') {
    $status_class = 'delivery';
}

elseif ($status == 'Delivered') {
    $status_class = 'delivered';
}

elseif ($status == 'Cancelled') {
    $status_class = 'cancelled';
}

?>


<div class="order-card">


<!-- TOP -->

<div class="order-top">

<div>

<div class="order-id">
Order #<?php echo $order['id']; ?>
</div>

<div class="order-date">

Placed on:
<?php
echo date(
    "d M Y, h:i A",
    strtotime($order['created_at'])
);
?>

</div>

</div>


<span class="status <?php echo $status_class; ?>">

<?php
echo htmlspecialchars($status);
?>

</span>

</div>


<!-- DETAILS -->

<div class="order-details">


<div class="detail-box">

<span class="detail-label">
Payment
</span>

<span class="detail-value">

<?php
echo htmlspecialchars(
    $order['payment_method']
);
?>

</span>

</div>


<div class="detail-box">

<span class="detail-label">
Order Status
</span>

<span class="detail-value">

<?php
echo htmlspecialchars($status);
?>

</span>

</div>


<div class="detail-box">

<span class="detail-label">
Order Amount
</span>

<span class="detail-value">

₹<?php
echo number_format(
    $order['total_amount'],
    2
);
?>

</span>

</div>


</div>


<!-- BOTTOM -->

<div class="order-bottom">

<div class="order-total">

Total:
₹<?php
echo number_format(
    $order['total_amount'],
    2
);
?>

</div>


<a
href="order_details.php?id=<?php echo $order['id']; ?>"
class="order-button"
>
View Order Details
</a>

</div>


</div>


<?php } ?>

</div>


<?php } ?>

</section>


<footer>

<p>
© 2026 FoodExpress |
Online Food Ordering System
</p>

</footer>

</body>

</html>