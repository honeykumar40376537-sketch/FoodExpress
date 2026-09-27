<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

/* Dashboard Statistics */

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders");
$total_orders = mysqli_fetch_assoc($result)['total'];

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
$total_users = mysqli_fetch_assoc($result)['total'];

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM foods");
$total_foods = mysqli_fetch_assoc($result)['total'];

$result = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(total_amount),0) AS total
     FROM orders
     WHERE order_status != 'Cancelled'"
);
$total_revenue = mysqli_fetch_assoc($result)['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard - FoodExpress</title>

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

/* TOP NAVBAR */

.admin-navbar {
    background: #ffffff;
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
    transition: 0.3s;
}

.admin-nav a:hover,
.admin-nav .active {
    background: #e63946;
    color: white;
}

/* MAIN */

.admin-container {
    width: 90%;
    max-width: 1200px;
    margin: 40px auto;
}

/* HEADER */

.dashboard-header {
    margin-bottom: 30px;
}

.dashboard-header h1 {
    font-size: 32px;
    margin-bottom: 8px;
}

.dashboard-header p {
    color: #666;
    font-size: 16px;
}

/* STAT CARDS */

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.07);
    transition: 0.3s;
    border-left: 5px solid #e63946;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

.stat-icon {
    font-size: 30px;
    margin-bottom: 15px;
}

.stat-card h3 {
    color: #666;
    font-size: 15px;
    margin-bottom: 10px;
}

.stat-card .number {
    font-size: 28px;
    font-weight: bold;
    color: #222;
}

/* QUICK ACTIONS */

.quick-section {
    margin-top: 35px;
}

.quick-section h2 {
    margin-bottom: 20px;
    font-size: 24px;
}

.quick-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
}

.quick-card {
    background: white;
    padding: 25px 20px;
    text-align: center;
    border-radius: 14px;
    text-decoration: none;
    color: #222;
    box-shadow: 0 5px 18px rgba(0,0,0,0.07);
    transition: 0.3s;
}

.quick-card:hover {
    transform: translateY(-5px);
    background: #fff5f3;
}

.quick-icon {
    font-size: 32px;
    margin-bottom: 12px;
}

.quick-card h3 {
    font-size: 17px;
    margin-bottom: 5px;
}

.quick-card p {
    font-size: 13px;
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

/* RESPONSIVE */

@media (max-width: 900px) {

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .quick-grid {
        grid-template-columns: repeat(2, 1fr);
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

    .admin-container {
        width: 92%;
        margin-top: 30px;
    }

    .dashboard-header h1 {
        font-size: 26px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .quick-grid {
        grid-template-columns: 1fr;
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

<a href="admin_dashboard.php" class="active">
Dashboard
</a>

<a href="admin_orders.php">
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


<!-- MAIN CONTENT -->

<div class="admin-container">

<div class="dashboard-header">

<h1>Admin Dashboard</h1>

<p>
Welcome back,
<strong>
<?php echo htmlspecialchars($_SESSION['admin_name']); ?>
</strong>
</p>

</div>


<!-- STATISTICS -->

<div class="stats-grid">

<div class="stat-card">

<div class="stat-icon">🛒</div>

<h3>Total Orders</h3>

<div class="number">
<?php echo $total_orders; ?>
</div>

</div>


<div class="stat-card">

<div class="stat-icon">👥</div>

<h3>Total Users</h3>

<div class="number">
<?php echo $total_users; ?>
</div>

</div>


<div class="stat-card">

<div class="stat-icon">🍔</div>

<h3>Food Items</h3>

<div class="number">
<?php echo $total_foods; ?>
</div>

</div>


<div class="stat-card">

<div class="stat-icon">💰</div>

<h3>Total Revenue</h3>

<div class="number">
₹<?php echo number_format($total_revenue, 2); ?>
</div>

</div>

</div>


<!-- QUICK ACTIONS -->

<div class="quick-section">

<h2>Quick Actions</h2>

<div class="quick-grid">

<a href="admin_orders.php" class="quick-card">

<div class="quick-icon">📦</div>

<h3>Manage Orders</h3>

<p>View and update customer orders</p>

</a>


<a href="admin_foods.php" class="quick-card">

<div class="quick-icon">🍕</div>

<h3>Manage Foods</h3>

<p>Add, edit and delete food items</p>

</a>


<a href="admin_categories.php" class="quick-card">

<div class="quick-icon">📂</div>

<h3>Manage Categories</h3>

<p>Manage food categories</p>

</a>


<a href="index.php" class="quick-card">

<div class="quick-icon">🌐</div>

<h3>View Website</h3>

<p>Open customer website</p>

</a>

</div>

</div>

</div>


<!-- FOOTER -->

<footer>

<p>
© 2026 FoodExpress | Online Food Ordering System
</p>

</footer>

</body>

</html>