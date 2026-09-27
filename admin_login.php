<?php
include "db.php";
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query(
        $conn,
        "SELECT * FROM admins WHERE email='$email' LIMIT 1"
    );

    if (mysqli_num_rows($result) == 1) {

        $admin = mysqli_fetch_assoc($result);

        if (password_verify($password, $admin['password'])) {

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

           header("Location: admin_dashboard.php");
            exit;

        } else {
            $message = "Wrong password.";
        }

    } else {
        $message = "Admin account not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Login - FoodExpress</title>

<link rel="stylesheet" href="css/style.css">

<style>
.admin-box {
    max-width:500px;
    margin:50px auto;
    padding:30px;
    background:white;
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.admin-box input {
    width:100%;
    padding:12px;
    margin:8px 0 15px;
    box-sizing:border-box;
    border:1px solid #ddd;
    border-radius:6px;
}

.admin-button {
    width:100%;
    padding:13px;
    border:none;
    border-radius:6px;
    background:#e91e3c;
    color:white;
    font-size:16px;
    cursor:pointer;
}

.message {
    color:red;
    margin-bottom:15px;
}
</style>

</head>

<body>

<nav class="navbar">
<div class="logo">FoodExpress Admin</div>

<div class="nav-links">
<a href="index.php">Home</a>
</div>
</nav>

<section class="section">

<div class="admin-box">

<h2>Admin Login</h2>

<?php if ($message != "") { ?>
<p class="message">
<?php echo htmlspecialchars($message); ?>
</p>
<?php } ?>

<form method="POST">

<label>Email</label>

<input
type="email"
name="email"
placeholder="Enter admin email"
required
>

<label>Password</label>

<input
type="password"
name="password"
placeholder="Enter admin password"
required
>

<button type="submit" class="admin-button">
Admin Login
</button>

</form>

</div>

</section>

<footer>
<p>© 2026 FoodExpress | Online Food Ordering System</p>
</footer>

</body>
</html>