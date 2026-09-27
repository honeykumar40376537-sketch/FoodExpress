<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$message = "";

/* Add Category */
if (isset($_POST['add_category'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);

    if ($name != "") {

        $check = mysqli_query(
            $conn,
            "SELECT id FROM categories WHERE name='$name' LIMIT 1"
        );

        if (mysqli_num_rows($check) > 0) {
            $message = "Category already exists.";
        } else {

            $query = mysqli_query(
                $conn,
                "INSERT INTO categories (name)
                 VALUES ('$name')"
            );

            if ($query) {
                $message = "Category added successfully.";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }
}

/* Delete Category */
if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    mysqli_query(
        $conn,
        "DELETE FROM categories WHERE id=$id"
    );

    header("Location: admin_categories.php");
    exit;
}

/* Get Categories */
$categories = mysqli_query(
    $conn,
    "SELECT * FROM categories ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Category Management - FoodExpress</title>

<link rel="stylesheet" href="css/style.css">

<style>

.category-box {
    max-width:600px;
    margin:30px auto;
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.category-box input {
    width:100%;
    padding:12px;
    margin:8px 0 15px;
    box-sizing:border-box;
    border:1px solid #ddd;
    border-radius:6px;
}

.admin-button {
    padding:12px 20px;
    border:none;
    background:#e91e3c;
    color:white;
    border-radius:6px;
    cursor:pointer;
}

.category-item {
    background:white;
    padding:18px;
    margin:10px 0;
    border-radius:8px;
    box-shadow:0 3px 12px rgba(0,0,0,0.07);
}

.delete-button {
    float:right;
    padding:7px 12px;
    background:#dc3545;
    color:white;
    text-decoration:none;
    border-radius:5px;
}

.message {
    text-align:center;
    color:green;
    font-weight:bold;
}

</style>

</head>

<body>

<nav class="navbar">

<div class="logo">FoodExpress Admin</div>

<div class="nav-links">

<a href="admin_dashboard.php">Dashboard</a>

<a href="admin_orders.php">Orders</a>

<a href="admin_foods.php">Foods</a>

<a href="admin_categories.php">Categories</a>

<a href="admin_logout.php">Logout</a>

</div>

</nav>

<section class="section">

<h2>Category Management</h2>

<?php if ($message != "") { ?>

<p class="message">
<?php echo htmlspecialchars($message); ?>
</p>

<?php } ?>

<div class="category-box">

<h3>Add New Category</h3>

<form method="POST">

<label>Category Name</label>

<input
type="text"
name="name"
placeholder="Example: South Indian"
required
>

<button
type="submit"
name="add_category"
class="admin-button"
>
Add Category
</button>

</form>

</div>

<h3>Existing Categories</h3>

<?php while ($category = mysqli_fetch_assoc($categories)) { ?>

<div class="category-item">

<strong>
<?php echo htmlspecialchars($category['name']); ?>
</strong>

<a
href="admin_categories.php?delete=<?php echo $category['id']; ?>"
class="delete-button"
onclick="return confirm('Are you sure you want to delete this category?');"
>
Delete
</a>

<div style="clear:both;"></div>

</div>

<?php } ?>

</section>

<footer>

<p>
© 2026 FoodExpress | Online Food Ordering System
</p>

</footer>

</body>

</html>