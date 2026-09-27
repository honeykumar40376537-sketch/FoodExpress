<?php
include "db.php";
session_start();

if (!isset($_GET['id'])) {
    die("Order ID missing.");
}

$order_id = intval($_GET['id']);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);

/* GET ORDER */
$order_result = mysqli_query(
    $conn,
    "SELECT *
     FROM orders
     WHERE id = $order_id
     AND user_id = $user_id
     LIMIT 1"
);

if (mysqli_num_rows($order_result) == 0) {
    die("Order not found.");
}

$order = mysqli_fetch_assoc($order_result);

$current_status = $order['order_status'];


/* GET ORDER ITEMS */
$items = mysqli_query(
    $conn,
    "SELECT order_items.*, foods.name
     FROM order_items
     LEFT JOIN foods
     ON order_items.food_id = foods.id
     WHERE order_items.order_id = $order_id"
);


/* TRACKING */
$statuses = [
    "Placed",
    "Confirmed",
    "Preparing",
    "Out for Delivery",
    "Delivered"
];

$current_index = array_search(
    $current_status,
    $statuses
);

if ($current_index === false) {
    $current_index = 0;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Order #<?php echo $order_id; ?> - FoodExpress
</title>

<link rel="stylesheet"
href="css/style.css">

<style>

/* =========================
   ORDER DETAILS
========================= */

.order-section {
    background:#f7f7f7;
    padding:45px 7%;
    min-height:75vh;
}

.order-container {
    max-width:1000px;
    margin:auto;
}


/* HEADER */

.order-header {
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,0.07);
    margin-bottom:22px;
}

.order-header-top {
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
}

.order-header h1 {
    font-size:28px;
}

.order-date {
    color:#777;
    margin-top:5px;
}


/* INFO */

.order-info {
    display:grid;
    grid-template-columns:
        repeat(3,1fr);
    gap:15px;
    margin-top:20px;
}

.info-box {
    background:#f7f7f7;
    padding:14px;
    border-radius:9px;
}

.info-label {
    display:block;
    color:#777;
    font-size:13px;
    margin-bottom:5px;
}

.info-value {
    font-weight:bold;
}


/* STATUS */

.current-status {
    padding:8px 15px;
    background:#d1e7dd;
    color:#0f5132;
    border-radius:20px;
    font-size:13px;
    font-weight:bold;
}


/* TRACKING */

.tracking {
    background:white;
    padding:28px;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,0.07);
    margin-bottom:22px;
}

.tracking h2 {
    margin-bottom:25px;
}

.tracking-step {
    display:flex;
    align-items:center;
    gap:15px;
    margin:0;
}

.tracking-circle {
    width:38px;
    height:38px;
    min-width:38px;
    border-radius:50%;
    background:#e5e5e5;
    color:#777;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
}

.tracking-step.active
.tracking-circle {
    background:#e63946;
    color:white;
}

.tracking-text {
    font-size:16px;
    color:#777;
}

.tracking-step.active
.tracking-text {
    color:#222;
    font-weight:bold;
}

.tracking-line {
    width:2px;
    height:28px;
    background:#ddd;
    margin-left:18px;
}

.tracking-line.active {
    background:#e63946;
}


/* ITEMS */

.items-box {
    background:white;
    padding:28px;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,0.07);
    margin-bottom:22px;
}

.items-box h2 {
    margin-bottom:20px;
}

.order-item {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:17px 0;
    border-bottom:1px solid #eee;
}

.order-item:last-child {
    border-bottom:none;
}

.item-name {
    font-weight:bold;
    font-size:17px;
}

.item-info {
    color:#777;
    font-size:14px;
    margin-top:4px;
}

.item-total {
    font-weight:bold;
    color:#e63946;
}


/* TOTAL */

.total-box {
    background:white;
    padding:25px 28px;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,0.07);
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.total-label {
    font-size:20px;
    font-weight:bold;
}

.total-price {
    font-size:25px;
    font-weight:bold;
    color:#e63946;
}


/* BACK BUTTON */

.back-button {
    display:inline-block;
    margin-top:20px;
    padding:12px 22px;
    background:#333;
    color:white;
    text-decoration:none;
    border-radius:8px;
    font-weight:bold;
}

.back-button:hover {
    background:#222;
}


/* MOBILE */

@media(max-width:700px) {

    .order-section {
        padding:35px 5%;
    }

    .order-header-top {
        flex-direction:column;
        align-items:flex-start;
    }

    .order-info {
        grid-template-columns:1fr;
    }

    .order-item {
        gap:15px;
    }

    .total-box {
        flex-direction:column;
        align-items:flex-start;
        gap:8px;
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


<!-- ORDER DETAILS -->

<section class="order-section">

<div class="order-container">


<!-- HEADER -->

<div class="order-header">

<div class="order-header-top">

<div>

<h1>
Order #<?php echo $order_id; ?>
</h1>

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


<div class="current-status">

<?php
echo htmlspecialchars($current_status);
?>

</div>

</div>


<div class="order-info">


<div class="info-box">

<span class="info-label">
Payment Method
</span>

<span class="info-value">

<?php
echo htmlspecialchars(
    $order['payment_method']
);
?>

</span>

</div>


<div class="info-box">

<span class="info-label">
Order Status
</span>

<span class="info-value">

<?php
echo htmlspecialchars(
    $current_status
);
?>

</span>

</div>


<div class="info-box">

<span class="info-label">
Total Amount
</span>

<span class="info-value">

₹<?php
echo number_format(
    $order['total_amount'],
    2
);
?>

</span>

</div>

</div>

</div>


<!-- TRACKING -->

<div class="tracking">

<h2>
Track Your Order
</h2>


<?php foreach (
    $statuses as $index => $status
) { ?>


<div class="tracking-step
<?php
if ($index <= $current_index) {
    echo ' active';
}
?>
">

<div class="tracking-circle">

<?php

if ($index <= $current_index) {
    echo "✓";
} else {
    echo ($index + 1);
}

?>

</div>


<div class="tracking-text">

<?php
echo $status;
?>

<?php
if ($status == $current_status) {
    echo " - Current Status";
}
?>

</div>

</div>


<?php if (
    $index < count($statuses) - 1
) { ?>

<div class="tracking-line
<?php
if ($index < $current_index) {
    echo ' active';
}
?>
"></div>

<?php } ?>


<?php } ?>

</div>


<!-- ITEMS -->

<div class="items-box">

<h2>
Ordered Items
</h2>


<?php while (
    $item = mysqli_fetch_assoc($items)
) { ?>

<div class="order-item">

<div>

<div class="item-name">

<?php
echo htmlspecialchars(
    $item['name']
);
?>

</div>

<div class="item-info">

Quantity:
<?php
echo $item['quantity'];
?>

&nbsp; × &nbsp;

₹<?php
echo number_format(
    $item['price'],
    2
);
?>

</div>

</div>


<div class="item-total">

₹<?php
echo number_format(
    $item['price'] * $item['quantity'],
    2
);
?>

</div>

</div>

<?php } ?>

</div>


<!-- TOTAL -->

<div class="total-box">

<div class="total-label">
Order Total
</div>

<div class="total-price">

₹<?php
echo number_format(
    $order['total_amount'],
    2
);
?>

</div>

</div>


<a
href="my_orders.php"
class="back-button"
>
← Back to My Orders
</a>


</div>

</section>


<footer>

<p>
© 2026 FoodExpress |
Online Food Ordering System
</p>

</footer>

</body>

</html>