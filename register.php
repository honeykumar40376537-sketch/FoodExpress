<?php
include "db.php";
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $check = mysqli_query(
        $conn,
        "SELECT id FROM users
         WHERE email='$email' OR phone='$phone'
         LIMIT 1"
    );

    if (mysqli_num_rows($check) > 0) {

        $message = "Email or phone already registered.";

    } else {

        $hashed_password = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $query = mysqli_query(
            $conn,
            "INSERT INTO users
            (name, email, phone, password, address)
            VALUES
            ('$name', '$email', '$phone',
             '$hashed_password', '$address')"
        );

        if ($query) {

            $user_id = mysqli_insert_id($conn);

            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;

            header("Location: index.php");
            exit;

        } else {

            $message = "Registration failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Create Account - FoodExpress</title>

<link rel="stylesheet"
href="css/style.css">

<style>

/* =========================
   REGISTER PAGE
========================= */

.register-section {
    min-height: calc(100vh - 70px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 50px 20px;
    background:
        linear-gradient(135deg, #fff5f2, #fff);
}

.register-container {
    width: 100%;
    max-width: 1000px;
    display: grid;
    grid-template-columns: 42% 58%;
    background: white;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 15px 45px rgba(0,0,0,0.12);
}


/* LEFT SIDE */

.register-left {
    background:
        linear-gradient(
            145deg,
            #e63946,
            #c92f3b
        );

    color: white;
    padding: 50px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.register-left::before {
    content: "";
    position: absolute;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,0.10);
    top: -70px;
    right: -70px;
}

.register-left::after {
    content: "";
    position: absolute;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    bottom: -60px;
    left: -60px;
}

.register-brand {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 18px;
    position: relative;
    z-index: 2;
}

.register-left h1 {
    font-size: 34px;
    line-height: 1.2;
    margin-bottom: 15px;
    position: relative;
    z-index: 2;
}

.register-left p {
    color: rgba(255,255,255,0.9);
    font-size: 16px;
    margin-bottom: 30px;
    position: relative;
    z-index: 2;
}

.benefits {
    position: relative;
    z-index: 2;
}

.benefit {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 17px 0;
    font-size: 15px;
}

.benefit-icon {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: rgba(255,255,255,0.18);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}


/* RIGHT SIDE */

.register-right {
    padding: 42px 45px;
}

.register-title {
    font-size: 29px;
    margin-bottom: 6px;
    color: #222;
}

.register-subtitle {
    color: #777;
    margin-bottom: 25px;
}


/* MESSAGE */

.register-message {
    background: #ffe6e6;
    color: #c62828;
    padding: 11px 14px;
    border-radius: 8px;
    margin-bottom: 18px;
    font-size: 14px;
}


/* FORM */

.register-form label {
    display: block;
    margin-top: 13px;
    margin-bottom: 6px;
    font-weight: bold;
    color: #333;
}

.register-form input,
.register-form textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #ddd;
    border-radius: 8px;
    outline: none;
    font-size: 15px;
    font-family: Arial, sans-serif;
    transition: 0.2s;
}

.register-form input:focus,
.register-form textarea:focus {
    border-color: #e63946;
    box-shadow: 0 0 0 3px rgba(230,57,70,0.08);
}

.register-form textarea {
    height: 80px;
    resize: vertical;
}


/* BUTTON */

.register-button {
    width: 100%;
    margin-top: 22px;
    padding: 14px;
    border: none;
    border-radius: 8px;
    background: #e63946;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

.register-button:hover {
    background: #c92f3b;
    transform: translateY(-1px);
}


/* LOGIN LINK */

.login-text {
    text-align: center;
    margin-top: 20px;
    color: #666;
}

.login-text a {
    color: #e63946;
    font-weight: bold;
    text-decoration: none;
}


/* MOBILE */

@media (max-width: 800px) {

    .register-container {
        grid-template-columns: 1fr;
    }

    .register-left {
        padding: 35px 30px;
    }

    .register-left h1 {
        font-size: 28px;
    }

    .register-right {
        padding: 35px 25px;
    }

}

@media (max-width: 480px) {

    .register-section {
        padding: 25px 12px;
    }

    .register-left {
        padding: 30px 22px;
    }

    .register-brand {
        font-size: 27px;
    }

    .register-left h1 {
        font-size: 25px;
    }

    .register-right {
        padding: 30px 20px;
    }

    .register-title {
        font-size: 25px;
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

<a href="login.php">
Login
</a>

<a href="register.php">
Register
</a>

<a href="cart.php">
Cart
</a>

</div>

</nav>


<!-- REGISTER -->

<section class="register-section">

<div class="register-container">


<!-- LEFT -->

<div class="register-left">

<div class="register-brand">
FoodExpress
</div>

<h1>
Join FoodExpress Today
</h1>

<p>
Create your account and order your favourite food quickly and easily.
</p>


<div class="benefits">

<div class="benefit">

<div class="benefit-icon">
✓
</div>

<span>
Fast & Easy Food Ordering
</span>

</div>


<div class="benefit">

<div class="benefit-icon">
✓
</div>

<span>
Track Your Orders
</span>

</div>


<div class="benefit">

<div class="benefit-icon">
✓
</div>

<span>
Save Your Delivery Details
</span>

</div>


<div class="benefit">

<div class="benefit-icon">
✓
</div>

<span>
Write Food Reviews
</span>

</div>

</div>

</div>


<!-- RIGHT -->

<div class="register-right">

<h2 class="register-title">
Create Account
</h2>

<p class="register-subtitle">
Fill in your details to get started.
</p>


<?php if ($message != "") { ?>

<div class="register-message">

<?php
echo htmlspecialchars($message);
?>

</div>

<?php } ?>


<form
method="POST"
class="register-form"
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
Email Address
</label>

<input
type="email"
name="email"
placeholder="Enter your email"
required
>


<label>
Mobile Number
</label>

<input
type="tel"
name="phone"
placeholder="Enter 10 digit mobile number"
pattern="[0-9]{10}"
maxlength="10"
required
>


<label>
Password
</label>

<input
type="password"
name="password"
placeholder="Create password"
minlength="6"
required
>


<label>
Delivery Address
</label>

<textarea
name="address"
placeholder="Enter your complete address"
required
></textarea>


<button
type="submit"
class="register-button"
>
Create My Account
</button>

</form>


<p class="login-text">

Already have an account?

<a href="login.php">
Login Now
</a>

</p>

</div>

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