<?php
include "db.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$subtotal = 0;

foreach ($_SESSION['cart'] as $food_id => $quantity) {

    $food_id = intval($food_id);
    $quantity = intval($quantity);

    $result = mysqli_query(
        $conn,
        "SELECT price FROM foods WHERE id = $food_id LIMIT 1"
    );

    $food = mysqli_fetch_assoc($result);

    if ($food) {
        $subtotal += $food['price'] * $quantity;
    }
}

$delivery_charge = 0;
$total = $subtotal + $delivery_charge;
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Checkout - FoodExpress</title>

<link rel="stylesheet"
href="css/style.css">

<style>

/* =========================
   CHECKOUT PAGE
========================= */

.checkout-section {
    padding: 45px 7%;
    background: #f7f7f7;
    min-height: 75vh;
}

.checkout-heading {
    text-align: center;
    margin-bottom: 35px;
}

.checkout-heading h1 {
    font-size: 32px;
    margin-bottom: 6px;
}

.checkout-heading p {
    color: #777;
}

.checkout-container {
    max-width: 1100px;
    margin: auto;
    display: grid;
    grid-template-columns: 1.5fr 0.8fr;
    gap: 25px;
}


/* LEFT */

.checkout-box {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.box-title {
    font-size: 22px;
    margin-bottom: 20px;
}


/* FORM */

.checkout-box label {
    display: block;
    margin-top: 15px;
    margin-bottom: 7px;
    font-weight: bold;
}

.checkout-box input,
.checkout-box textarea,
.checkout-box select {
    width: 100%;
    padding: 13px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-sizing: border-box;
    font-size: 15px;
    outline: none;
}

.checkout-box input:focus,
.checkout-box textarea:focus,
.checkout-box select:focus {
    border-color: #e63946;
}

.checkout-box textarea {
    height: 95px;
    resize: vertical;
}


/* PAYMENT */

.payment-title {
    margin-top: 25px;
    font-size: 20px;
}

.payment-box {
    margin-top: 12px;
    padding: 18px;
    background: #fff8e8;
    border: 1px solid #ffd86b;
    border-radius: 10px;
}

.payment-box p {
    color: #666;
    font-size: 14px;
}

.payment-box label {
    margin-top: 12px;
}


/* PLACE ORDER */

.place-button {
    width: 100%;
    margin-top: 25px;
    padding: 15px;
    border: none;
    border-radius: 9px;
    background: #e63946;
    color: white;
    font-size: 17px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

.place-button:hover {
    background: #c92f3b;
}


/* RIGHT SUMMARY */

.summary-box {
    background: white;
    padding: 28px;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    height: fit-content;
    position: sticky;
    top: 90px;
}

.summary-box h2 {
    margin-bottom: 22px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin: 15px 0;
    color: #555;
}

.summary-row strong {
    color: #222;
}

.summary-divider {
    border: none;
    border-top: 1px solid #ddd;
    margin: 20px 0;
}

.total-row {
    display: flex;
    justify-content: space-between;
    font-size: 22px;
    font-weight: bold;
}

.total-price {
    color: #e63946;
}

.secure-box {
    margin-top: 25px;
    padding: 15px;
    background: #f5f5f5;
    border-radius: 8px;
    font-size: 13px;
    color: #666;
    text-align: center;
}


/* MOBILE */

@media (max-width: 800px) {

    .checkout-section {
        padding: 35px 5%;
    }

    .checkout-container {
        grid-template-columns: 1fr;
    }

    .summary-box {
        position: static;
    }

}

@media (max-width: 480px) {

    .checkout-box,
    .summary-box {
        padding: 22px;
    }

    .checkout-heading h1 {
        font-size: 27px;
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

<a href="cart.php">
Cart
</a>

</div>

</nav>


<!-- CHECKOUT -->

<section class="checkout-section">

<div class="checkout-heading">

<h1>
Complete Your Order
</h1>

<p>
Enter your delivery details and select your payment method.
</p>

</div>


<div class="checkout-container">


<!-- LEFT SIDE -->

<div class="checkout-box">

<h2 class="box-title">
Delivery Details
</h2>

<form
method="POST"
action="place_order.php"
>

<label>
Full Name
</label>

<input
type="text"
name="name"
placeholder="Enter your full name"
required
>


<label>
Mobile Number
</label>

<input
type="tel"
name="mobile"
placeholder="Enter 10 digit mobile number"
pattern="[0-9]{10}"
maxlength="10"
required
>


<label>
Delivery Address
</label>

<textarea
name="address"
placeholder="Enter your complete delivery address"
required
></textarea>


<h3 class="payment-title">
Payment Method
</h3>

<select
name="payment"
id="payment"
required
onchange="showPaymentBox()"
>

<option value="COD">
Cash on Delivery
</option>

<option value="UPI">
UPI
</option>

<option value="CARD">
Credit / Debit Card
</option>

<option value="NETBANKING">
Net Banking
</option>

</select>


<!-- UPI -->

<div
id="upiBox"
class="payment-box"
style="display:none;"
>

<strong>
UPI Payment
</strong>

<p>
Demo payment — no real money will be charged.
</p>

<input
type="text"
name="upi_id"
placeholder="example@upi"
>

</div>


<!-- CARD -->

<div
id="cardBox"
class="payment-box"
style="display:none;"
>

<strong>
Card Payment
</strong>

<p>
Demo payment — no real transaction.
</p>

<label>
Card Number
</label>

<input
type="text"
name="card_number"
placeholder="XXXX XXXX XXXX XXXX"
maxlength="19"
>

<label>
Expiry Date
</label>

<input
type="text"
name="expiry"
placeholder="MM/YY"
maxlength="5"
>

<label>
CVV
</label>

<input
type="password"
name="cvv"
placeholder="XXX"
maxlength="3"
>

</div>


<!-- NET BANKING -->

<div
id="bankBox"
class="payment-box"
style="display:none;"
>

<strong>
Net Banking
</strong>

<p>
Select your bank for demo payment.
</p>

<select name="bank">

<option value="">
Select Bank
</option>

<option value="SBI">
State Bank of India
</option>

<option value="HDFC">
HDFC Bank
</option>

<option value="ICICI">
ICICI Bank
</option>

<option value="PNB">
Punjab National Bank
</option>

</select>

</div>


<button
type="submit"
class="place-button"
>
Place Order
</button>

</form>

</div>


<!-- RIGHT SIDE -->

<div class="summary-box">

<h2>
Order Summary
</h2>

<div class="summary-row">

<span>
Subtotal
</span>

<strong>
₹<?php echo number_format($subtotal, 2); ?>
</strong>

</div>


<div class="summary-row">

<span>
Delivery Charge
</span>

<strong>
₹<?php echo number_format($delivery_charge, 2); ?>
</strong>

</div>


<hr class="summary-divider">


<div class="total-row">

<span>
Total
</span>

<span class="total-price">
₹<?php echo number_format($total, 2); ?>
</span>

</div>


<div class="secure-box">

Your order information is securely processed for this college project.

</div>

</div>

</div>

</section>


<footer>

<p>
© 2026 FoodExpress |
Online Food Ordering System
</p>

</footer>


<script>

function showPaymentBox() {

    let payment =
        document.getElementById("payment").value;

    document.getElementById("upiBox").style.display =
        "none";

    document.getElementById("cardBox").style.display =
        "none";

    document.getElementById("bankBox").style.display =
        "none";


    if (payment === "UPI") {

        document.getElementById("upiBox").style.display =
            "block";

    }

    else if (payment === "CARD") {

        document.getElementById("cardBox").style.display =
            "block";

    }

    else if (payment === "NETBANKING") {

        document.getElementById("bankBox").style.display =
            "block";

    }

}

</script>

</body>

</html>