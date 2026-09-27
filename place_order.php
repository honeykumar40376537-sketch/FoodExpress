<?php
include "db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$mobile = mysqli_real_escape_string($conn, $_POST['mobile'] ?? '');
$address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
$payment = mysqli_real_escape_string($conn, $_POST['payment'] ?? 'COD');

if ($name == '' || $mobile == '' || $address == '') {
    die("Please fill all required details.");
}

/* Find or create customer */

 if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);

$user_result = mysqli_query(
    $conn,
    "SELECT id FROM users WHERE id = $user_id LIMIT 1"
);

if (mysqli_num_rows($user_result) == 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}


/* IMPORTANT: Save user ID in session */

$_SESSION['user_id'] = $user_id;


/* Create delivery address */

$address_sql = mysqli_query(
    $conn,
    "INSERT INTO addresses
    (user_id, full_name, phone, address)
    VALUES
    ('$user_id', '$name', '$mobile', '$address')"
);

if (!$address_sql) {
    die("Address creation failed: " . mysqli_error($conn));
}

$address_id = mysqli_insert_id($conn);


/* Calculate total */

$subtotal = 0;

foreach ($_SESSION['cart'] as $food_id => $quantity) {

    $food_id = intval($food_id);
    $quantity = intval($quantity);

    $result = mysqli_query(
        $conn,
        "SELECT price FROM foods
         WHERE id = $food_id
         LIMIT 1"
    );

    $food = mysqli_fetch_assoc($result);

    if ($food) {
        $subtotal += $food['price'] * $quantity;
    }
}

$discount = 0;
$delivery_charge = 0;
$total_amount = $subtotal;


/* Create order */

$order_sql = mysqli_query(
    $conn,
    "INSERT INTO orders
    (user_id, address_id, coupon_id, subtotal, discount,
     delivery_charge, total_amount, payment_method, order_status)
    VALUES
    ('$user_id', '$address_id', NULL, '$subtotal', '$discount',
     '$delivery_charge', '$total_amount', '$payment', 'Placed')"
);

if (!$order_sql) {
    die("Order creation failed: " . mysqli_error($conn));
}

$order_id = mysqli_insert_id($conn);


/* Save order items */

foreach ($_SESSION['cart'] as $food_id => $quantity) {

    $food_id = intval($food_id);
    $quantity = intval($quantity);

    $result = mysqli_query(
        $conn,
        "SELECT price FROM foods
         WHERE id = $food_id
         LIMIT 1"
    );

    $food = mysqli_fetch_assoc($result);

    if ($food) {

        $price = $food['price'];

        mysqli_query(
            $conn,
            "INSERT INTO order_items
            (order_id, food_id, quantity, price)
            VALUES
            ('$order_id', '$food_id', '$quantity', '$price')"
        );
    }
}


/* Empty cart */

$_SESSION['cart'] = [];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Order Confirmed</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<nav class="navbar">

    <div class="logo">
        FoodExpress
    </div>

    <div class="nav-links">

        <a href="index.php">Home</a>

        <a href="index.php#foods">Menu</a>

        <a href="my_orders.php">My Orders</a>

        <a href="cart.php">Cart</a>

    </div>

</nav>


<section class="section">

    <h2>Order Placed Successfully!</h2>

    <p>
        Thank you,
        <?php echo htmlspecialchars($name); ?>.
    </p>

    <p>
        Your Order ID:
        <strong>#<?php echo $order_id; ?></strong>
    </p>

    <p>
        Payment Method:
        <strong>
            <?php echo htmlspecialchars($payment); ?>
        </strong>
    </p>

    <p>
        Total Amount:
        <strong>
            ₹<?php echo number_format($total_amount, 2); ?>
        </strong>
    </p>

    <p>
        Order Status:
        <strong>Placed</strong>
    </p>

    <br>

    <a href="my_orders.php" class="add-cart">
        View My Orders
    </a>

</section>


<footer>

    <p>
        © 2026 FoodExpress |
        Online Food Ordering System
    </p>

</footer>

</body>

</html>