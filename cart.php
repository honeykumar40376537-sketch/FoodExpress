<?php
include "db.php";

session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* ADD TO CART */
if (isset($_GET['add'])) {

    $food_id = intval($_GET['add']);

    if (isset($_SESSION['cart'][$food_id])) {
        $_SESSION['cart'][$food_id]++;
    } else {
        $_SESSION['cart'][$food_id] = 1;
    }

    header("Location: cart.php");
    exit;
}

/* REMOVE FROM CART */
if (isset($_GET['remove'])) {

    $food_id = intval($_GET['remove']);

    unset($_SESSION['cart'][$food_id]);

    header("Location: cart.php");
    exit;
}

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Your Cart - FoodExpress</title>

<link rel="stylesheet"
href="css/style.css">

<style>

/* =========================
   CART PAGE
========================= */

.cart-section {
    padding: 45px 7%;
    min-height: 70vh;
    background: #f7f7f7;
}

.cart-heading {
    margin-bottom: 30px;
}

.cart-heading h1 {
    font-size: 32px;
    margin-bottom: 5px;
}

.cart-heading p {
    color: #777;
}

.cart-layout {
    max-width: 1100px;
    margin: auto;
    display: grid;
    grid-template-columns: 1.5fr 0.7fr;
    gap: 25px;
}


/* CART ITEMS */

.cart-items {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.cart-item {
    background: white;
    padding: 18px;
    border-radius: 14px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.07);

    display: grid;
    grid-template-columns: 120px 1fr auto;
    gap: 18px;
    align-items: center;
}

.cart-image {
    width: 120px;
    height: 100px;
    border-radius: 10px;
    overflow: hidden;
}

.cart-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cart-info h3 {
    margin-bottom: 8px;
    font-size: 19px;
}

.cart-info p {
    color: #666;
    margin: 4px 0;
    font-size: 14px;
}

.cart-subtotal {
    color: #e63946 !important;
    font-size: 17px !important;
    font-weight: bold;
}

.remove-button {
    display: inline-block;
    padding: 9px 13px;
    background: #ffe5e5;
    color: #d62839;
    text-decoration: none;
    border-radius: 7px;
    font-size: 13px;
    font-weight: bold;
}

.remove-button:hover {
    background: #d62839;
    color: white;
}


/* SUMMARY */

.cart-summary {
    background: white;
    padding: 28px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    height: fit-content;
    position: sticky;
    top: 90px;
}

.cart-summary h2 {
    margin-bottom: 22px;
}

.summary-line {
    display: flex;
    justify-content: space-between;
    margin: 15px 0;
    color: #555;
}

.summary-line strong {
    color: #222;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    font-size: 22px;
    font-weight: bold;
    margin-top: 20px;
}

.summary-total span:last-child {
    color: #e63946;
}

.summary-divider {
    border: none;
    border-top: 1px solid #ddd;
    margin: 20px 0;
}


/* BUTTONS */

.cart-button {
    display: block;
    width: 100%;
    text-align: center;
    padding: 13px;
    margin-top: 12px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    box-sizing: border-box;
}

.continue-button {
    background: #f1f1f1;
    color: #333;
}

.continue-button:hover {
    background: #ddd;
}

.checkout-button {
    background: #e63946;
    color: white;
}

.checkout-button:hover {
    background: #c92f3b;
}


/* EMPTY CART */

.empty-cart {
    max-width: 550px;
    margin: 50px auto;
    background: white;
    padding: 50px 30px;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.empty-icon {
    font-size: 55px;
    margin-bottom: 15px;
}

.empty-cart h2 {
    margin-bottom: 10px;
}

.empty-cart p {
    color: #777;
    margin-bottom: 25px;
}


/* MOBILE */

@media (max-width: 800px) {

    .cart-layout {
        grid-template-columns: 1fr;
    }

    .cart-summary {
        position: static;
    }

}

@media (max-width: 600px) {

    .cart-section {
        padding: 35px 5%;
    }

    .cart-item {
        grid-template-columns: 90px 1fr;
    }

    .cart-image {
        width: 90px;
        height: 80px;
    }

    .cart-item .remove-button {
        grid-column: 2;
        width: fit-content;
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

<a href="index.php#categories">
Categories
</a>

<a href="index.php#foods">
Menu
</a>

<a href="cart.php">
Cart
</a>

</div>

</nav>


<!-- CART -->

<section class="cart-section">

<div class="cart-heading">

<h1>
Your Shopping Cart
</h1>

<p>
Review your items before placing your order.
</p>

</div>


<?php if (empty($_SESSION['cart'])) { ?>


<!-- EMPTY CART -->

<div class="empty-cart">

<div class="empty-icon">
🛒
</div>

<h2>
Your cart is empty
</h2>

<p>
Looks like you haven't added any food yet.
</p>

<a
href="index.php"
class="cart-button checkout-button"
>
Explore Food
</a>

</div>


<?php } else { ?>


<div class="cart-layout">


<!-- ITEMS -->

<div class="cart-items">

<?php

foreach ($_SESSION['cart'] as $food_id => $quantity) {

    $food_id = intval($food_id);

    $result = mysqli_query(
        $conn,
        "SELECT * FROM foods WHERE id = $food_id"
    );

    $food = mysqli_fetch_assoc($result);

    if (!$food) {
        continue;
    }

    $subtotal = $food['price'] * $quantity;

    $total += $subtotal;

?>

<div class="cart-item">


<!-- IMAGE -->

<div class="cart-image">

<img
src="img/<?php echo htmlspecialchars($food['image']); ?>"
alt="<?php echo htmlspecialchars($food['name']); ?>"
onerror="this.src='img/food.jpg';"
>

</div>


<!-- INFO -->

<div class="cart-info">

<h3>
<?php echo htmlspecialchars($food['name']); ?>
</h3>

<p>
Price:
₹<?php echo number_format($food['price'], 2); ?>
</p>

<p>
Quantity:
<?php echo $quantity; ?>
</p>

<p class="cart-subtotal">
Subtotal:
₹<?php echo number_format($subtotal, 2); ?>
</p>

</div>


<!-- REMOVE -->

<div>

<a
href="cart.php?remove=<?php echo $food_id; ?>"
class="remove-button"
>
Remove
</a>

</div>

</div>

<?php } ?>

</div>


<!-- SUMMARY -->

<div class="cart-summary">

<h2>
Order Summary
</h2>


<div class="summary-line">

<span>
Items
</span>

<strong>
<?php echo count($_SESSION['cart']); ?>
</strong>

</div>


<div class="summary-line">

<span>
Delivery
</span>

<strong>
₹0.00
</strong>

</div>


<hr class="summary-divider">


<div class="summary-total">

<span>
Total
</span>

<span>
₹<?php echo number_format($total, 2); ?>
</span>

</div>


<a
href="index.php"
class="cart-button continue-button"
>
Continue Shopping
</a>


<a
href="checkout.php"
class="cart-button checkout-button"
>
Proceed to Checkout
</a>

</div>

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