<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

/* UPDATE ORDER STATUS */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $order_id = intval($_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $allowed = [
        'Placed',
        'Confirmed',
        'Preparing',
        'Out for Delivery',
        'Delivered',
        'Cancelled'
    ];

    if (in_array($status, $allowed)) {
        mysqli_query(
            $conn,
            "UPDATE orders
             SET order_status = '$status'
             WHERE id = $order_id"
        );
    }

    header("Location: admin_orders.php");
    exit;
}

/* GET ORDERS */
$orders = mysqli_query($conn, "
    SELECT orders.*, users.name, users.phone
    FROM orders
    LEFT JOIN users ON orders.user_id = users.id
    ORDER BY orders.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Orders - FoodExpress</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f5f6fa;
    color: #222;
}

/* NAVBAR */

.admin-navbar {
    background: white;
    padding: 18px 6%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.admin-logo {
    font-size: 25px;
    font-weight: bold;
    color: #e63946;
}

.admin-nav {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.admin-nav a {
    text-decoration: none;
    color: #333;
    padding: 9px 14px;
    border-radius: 7px;
    font-weight: 600;
}

.admin-nav a:hover,
.admin-nav .active {
    background: #e63946;
    color: white;
}

/* CONTAINER */

.orders-container {
    width: 90%;
    max-width: 1200px;
    margin: 40px auto;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 32px;
    margin-bottom: 8px;
}

.page-header p {
    color: #777;
}

/* ORDER GRID */

.orders-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 22px;
}

/* ORDER CARD */

.order-card {
    background: white;
    border-radius: 15px;
    padding: 24px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.07);
}

.order-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}

.order-id {
    font-size: 20px;
    font-weight: bold;
}

/* STATUS */

.status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.status-placed {
    background: #fff3cd;
    color: #856404;
}

.status-confirmed {
    background: #dbeafe;
    color: #1d4ed8;
}

.status-preparing {
    background: #fef3c7;
    color: #92400e;
}

.status-delivery {
    background: #e0e7ff;
    color: #4338ca;
}

.status-delivered {
    background: #dcfce7;
    color: #166534;
}

.status-cancelled {
    background: #fee2e2;
    color: #b91c1c;
}

/* ORDER DETAILS */

.order-details {
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
    padding: 15px 0;
    margin-bottom: 18px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 10px;
    font-size: 14px;
}

.detail-row:last-child {
    margin-bottom: 0;
}

.detail-label {
    color: #777;
}

.detail-value {
    font-weight: 600;
    text-align: right;
}

.total {
    color: #e63946;
    font-size: 17px;
}

/* FOOD ITEMS */

.items-title {
    font-size: 15px;
    font-weight: bold;
    margin-bottom: 12px;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
    background: #f8f8f8;
    border-radius: 9px;
    margin-bottom: 10px;
}

.order-item-image {
    width: 65px;
    height: 65px;
    border-radius: 8px;
    object-fit: cover;
    background: #eee;
    flex-shrink: 0;
}

.order-item-info {
    flex: 1;
}

.order-item-name {
    font-weight: bold;
    margin-bottom: 4px;
}

.order-item-details {
    font-size: 13px;
    color: #777;
}

.order-item-price {
    font-weight: bold;
    color: #e63946;
}

/* UPDATE */

.update-title {
    font-size: 14px;
    font-weight: bold;
    margin-top: 20px;
    margin-bottom: 8px;
}

.status-form {
    display: flex;
    gap: 10px;
}

.status-form select {
    flex: 1;
    padding: 11px;
    border: 1px solid #ddd;
    border-radius: 7px;
    background: white;
}

.update-btn {
    border: none;
    padding: 11px 16px;
    background: #e63946;
    color: white;
    border-radius: 7px;
    font-weight: bold;
    cursor: pointer;
}

.update-btn:hover {
    background: #c92f3b;
}

/* EMPTY */

.empty-orders {
    background: white;
    padding: 60px 20px;
    text-align: center;
    border-radius: 15px;
    color: #777;
}

/* FOOTER */

footer {
    background: #222;
    color: #ddd;
    text-align: center;
    padding: 25px;
    margin-top: 50px;
}

/* MOBILE */

@media (max-width: 850px) {

    .orders-grid {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 600px) {

    .admin-navbar {
        flex-direction: column;
        gap: 15px;
    }

    .admin-nav {
        justify-content: center;
    }

    .orders-container {
        width: 92%;
        margin-top: 30px;
    }

    .page-header h1 {
        font-size: 27px;
    }

    .order-top {
        align-items: flex-start;
        gap: 10px;
        flex-direction: column;
    }

    .status-form {
        flex-direction: column;
    }

    .update-btn {
        width: 100%;
    }

}

</style>

</head>

<body>

<!-- NAVBAR -->

<nav class="admin-navbar">

<div class="admin-logo">
FoodExpress Admin
</div>

<div class="admin-nav">

<a href="admin_dashboard.php">
Dashboard
</a>

<a href="admin_orders.php" class="active">
Orders
</a>

<a href="admin_foods.php">
Foods
</a>

<a href="admin_categories.php">
Categories
</a>

<a href="index.php">
Website
</a>

<a href="admin_logout.php">
Logout
</a>

</div>

</nav>


<!-- MAIN -->

<div class="orders-container">

<div class="page-header">

<h1>Order Management</h1>

<p>
View customer orders, ordered food items and update order status.
</p>

</div>


<?php if (mysqli_num_rows($orders) == 0) { ?>

<div class="empty-orders">

<h2>No Orders Found</h2>

<p>
There are currently no customer orders.
</p>

</div>

<?php } else { ?>


<div class="orders-grid">


<?php while ($order = mysqli_fetch_assoc($orders)) { ?>


<?php

$status_class = "status-placed";

if ($order['order_status'] == 'Confirmed') {
    $status_class = "status-confirmed";
}

if ($order['order_status'] == 'Preparing') {
    $status_class = "status-preparing";
}

if ($order['order_status'] == 'Out for Delivery') {
    $status_class = "status-delivery";
}

if ($order['order_status'] == 'Delivered') {
    $status_class = "status-delivered";
}

if ($order['order_status'] == 'Cancelled') {
    $status_class = "status-cancelled";
}


/* GET ORDER ITEMS */

$order_id = intval($order['id']);

$items = mysqli_query($conn, "
    SELECT order_items.*, foods.name, foods.image
    FROM order_items
    LEFT JOIN foods
    ON order_items.food_id = foods.id
    WHERE order_items.order_id = $order_id
");

?>


<div class="order-card">


<div class="order-top">

<div class="order-id">
Order #<?php echo $order['id']; ?>
</div>

<div class="status <?php echo $status_class; ?>">
<?php echo htmlspecialchars($order['order_status']); ?>
</div>

</div>


<!-- CUSTOMER DETAILS -->

<div class="order-details">

<div class="detail-row">

<span class="detail-label">
Customer
</span>

<span class="detail-value">
<?php echo htmlspecialchars($order['name'] ?? 'Guest'); ?>
</span>

</div>


<div class="detail-row">

<span class="detail-label">
Mobile
</span>

<span class="detail-value">
<?php echo htmlspecialchars($order['phone'] ?? '-'); ?>
</span>

</div>


<div class="detail-row">

<span class="detail-label">
Order Date
</span>

<span class="detail-value">
<?php echo date("d M Y, h:i A", strtotime($order['created_at'])); ?>
</span>

</div>


<div class="detail-row">

<span class="detail-label">
Payment
</span>

<span class="detail-value">
<?php echo htmlspecialchars($order['payment_method']); ?>
</span>

</div>


<div class="detail-row">

<span class="detail-label">
Total Amount
</span>

<span class="detail-value total">
₹<?php echo number_format($order['total_amount'], 2); ?>
</span>

</div>

</div>


<!-- ORDERED FOOD -->

<div class="items-title">
Ordered Food
</div>


<?php if (mysqli_num_rows($items) > 0) { ?>


<?php while ($item = mysqli_fetch_assoc($items)) { ?>


<?php

$image_path = "img/" . $item['image'];

?>


<div class="order-item">


<?php if (!empty($item['image']) && file_exists($image_path)) { ?>

<img
src="<?php echo htmlspecialchars($image_path); ?>"
class="order-item-image"
alt="<?php echo htmlspecialchars($item['name']); ?>"
>

<?php } else { ?>

<div class="order-item-image"
style="display:flex;align-items:center;justify-content:center;">
🍽️
</div>

<?php } ?>


<div class="order-item-info">

<div class="order-item-name">
<?php echo htmlspecialchars($item['name']); ?>
</div>

<div class="order-item-details">

Quantity:
<?php echo intval($item['quantity']); ?>

× ₹<?php echo number_format($item['price'], 2); ?>

</div>

</div>


<div class="order-item-price">

₹<?php
echo number_format(
    $item['price'] * $item['quantity'],
    2
);
?>

</div>


</div>


<?php } ?>


<?php } else { ?>

<p style="color:#777;font-size:13px;">
No food items found.
</p>

<?php } ?>


<!-- UPDATE STATUS -->

<div class="update-title">
Update Order Status
</div>


<form method="POST" class="status-form">

<input
type="hidden"
name="order_id"
value="<?php echo $order['id']; ?>"
>


<select name="status">

<option value="Placed"
<?php if ($order['order_status'] == 'Placed') echo 'selected'; ?>>
Placed
</option>

<option value="Confirmed"
<?php if ($order['order_status'] == 'Confirmed') echo 'selected'; ?>>
Confirmed
</option>

<option value="Preparing"
<?php if ($order['order_status'] == 'Preparing') echo 'selected'; ?>>
Preparing
</option>

<option value="Out for Delivery"
<?php if ($order['order_status'] == 'Out for Delivery') echo 'selected'; ?>>
Out for Delivery
</option>

<option value="Delivered"
<?php if ($order['order_status'] == 'Delivered') echo 'selected'; ?>>
Delivered
</option>

<option value="Cancelled"
<?php if ($order['order_status'] == 'Cancelled') echo 'selected'; ?>>
Cancelled
</option>

</select>


<button
type="submit"
class="update-btn"
>
Update
</button>

</form>


</div>


<?php } ?>


</div>


<?php } ?>


</div>


<footer>

<p>
© 2026 FoodExpress | Online Food Ordering System
</p>

</footer>


</body>

</html>