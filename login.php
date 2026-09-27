<?php
include "db.php";
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query(
        $conn,
        "SELECT * FROM users WHERE email='$email' LIMIT 1"
    );

    if (mysqli_num_rows($query) == 1) {

        $user = mysqli_fetch_assoc($query);

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header("Location: index.php");
            exit;

        } else {
            $message = "Invalid email or password.";
        }

    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login - FoodExpress</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    min-height: 100vh;
    background: #f7f7f7;
}

.login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px;
}

.login-container {
    width: 100%;
    max-width: 1050px;
    min-height: 620px;
    background: white;
    border-radius: 25px;
    overflow: hidden;
    display: grid;
    grid-template-columns: 1fr 1fr;
    box-shadow: 0 15px 50px rgba(0,0,0,0.12);
}

/* LEFT SIDE */

.login-left {
    background: linear-gradient(145deg, #e63946, #b91f2d);
    color: white;
    padding: 60px 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.login-left::before {
    content: "";
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
    position: absolute;
    top: -100px;
    right: -100px;
}

.login-left::after {
    content: "";
    width: 220px;
    height: 220px;
    background: rgba(255,255,255,0.07);
    border-radius: 50%;
    position: absolute;
    bottom: -80px;
    left: -80px;
}

.brand {
    font-size: 34px;
    font-weight: bold;
    margin-bottom: 25px;
    position: relative;
    z-index: 2;
}

.login-left h1 {
    font-size: 42px;
    line-height: 1.15;
    margin-bottom: 20px;
    position: relative;
    z-index: 2;
}

.login-left p {
    font-size: 17px;
    line-height: 1.7;
    opacity: 0.9;
    max-width: 430px;
    position: relative;
    z-index: 2;
}

.food-icons {
    display: flex;
    gap: 15px;
    margin-top: 35px;
    position: relative;
    z-index: 2;
}

.food-icon {
    width: 65px;
    height: 65px;
    border-radius: 18px;
    background: rgba(255,255,255,0.18);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
}

/* RIGHT SIDE */

.login-right {
    padding: 55px 65px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.login-right h2 {
    font-size: 32px;
    color: #222;
    margin-bottom: 8px;
}

.subtitle {
    color: #777;
    margin-bottom: 30px;
}

.message {
    background: #ffe5e5;
    color: #c62828;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
    color: #333;
}

.form-group input {
    width: 100%;
    padding: 14px 16px;
    border: 1px solid #ddd;
    border-radius: 9px;
    font-size: 15px;
    outline: none;
    transition: 0.3s;
}

.form-group input:focus {
    border-color: #e63946;
    box-shadow: 0 0 0 3px rgba(230,57,70,0.1);
}

.login-btn {
    width: 100%;
    padding: 15px;
    border: none;
    border-radius: 9px;
    background: #e63946;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 5px;
    transition: 0.3s;
}

.login-btn:hover {
    background: #c92f3b;
    transform: translateY(-2px);
}

.register-text {
    text-align: center;
    margin-top: 25px;
    color: #777;
}

.register-text a {
    color: #e63946;
    text-decoration: none;
    font-weight: bold;
}

.register-text a:hover {
    text-decoration: underline;
}

.back-home {
    text-align: center;
    margin-top: 15px;
}

.back-home a {
    color: #555;
    text-decoration: none;
    font-size: 14px;
}

/* MOBILE */

@media (max-width: 800px) {

    .login-page {
        padding: 15px;
    }

    .login-container {
        grid-template-columns: 1fr;
    }

    .login-left {
        padding: 40px 30px;
        min-height: 300px;
    }

    .login-left h1 {
        font-size: 30px;
    }

    .brand {
        font-size: 28px;
    }

    .login-right {
        padding: 40px 30px;
    }

}

</style>

</head>

<body>

<div class="login-page">

    <div class="login-container">

        <!-- LEFT -->

        <div class="login-left">

            <div class="brand">FoodExpress</div>

            <h1>
                Delicious food,<br>
                delivered to you.
            </h1>

            <p>
                Login to your FoodExpress account and order
                your favourite food quickly and easily.
            </p>

            <div class="food-icons">
                <div class="food-icon">🍕</div>
                <div class="food-icon">🍔</div>
                <div class="food-icon">🍜</div>
                <div class="food-icon">🍰</div>
            </div>

        </div>

        <!-- RIGHT -->

        <div class="login-right">

            <h2>Welcome Back!</h2>

            <p class="subtitle">
                Login to continue ordering delicious food.
            </p>

            <?php if ($message != "") { ?>

                <div class="message">
                    <?php echo $message; ?>
                </div>

            <?php } ?>

            <form method="POST">

                <div class="form-group">

                    <label>Email Address</label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Password</label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                </div>

                <button type="submit" class="login-btn">
                    Login to FoodExpress
                </button>

            </form>

            <div class="register-text">
                Don't have an account?
                <a href="register.php">Create Account</a>
            </div>

            <div class="back-home">
                <a href="index.php">← Back to Home</a>
            </div>

        </div>

    </div>

</div>

</body>
</html>