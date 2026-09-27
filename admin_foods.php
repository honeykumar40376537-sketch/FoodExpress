<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$message = "";

/* UPDATE FOOD */
if (isset($_POST['update_food'])) {

    $id = intval($_POST['food_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);

    $query = mysqli_query($conn, "
        UPDATE foods SET
        name='$name',
        description='$description',
        price='$price',
        category_id='$category_id',
        image='$image'
        WHERE id=$id
    ");

    if ($query) {
        $message = "Food updated successfully.";
    } else {
        $message = "Update failed: " . mysqli_error($conn);
    }
}

/* ADD FOOD */
if (isset($_POST['add_food'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);

    $query = mysqli_query($conn, "
        INSERT INTO foods
        (category_id, name, description, price, image)
        VALUES
        ('$category_id','$name','$description','$price','$image')
    ");

    if ($query) {
        $message = "Food added successfully.";
    } else {
        $message = "Add failed: " . mysqli_error($conn);
    }
}

/* DELETE FOOD */
if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    mysqli_query($conn, "DELETE FROM foods WHERE id=$id");

    header("Location: admin_foods.php");
    exit;
}

/* EDIT FOOD */
$edit_food = null;

if (isset($_GET['edit'])) {

    $id = intval($_GET['edit']);

    $result = mysqli_query(
        $conn,
        "SELECT * FROM foods WHERE id=$id LIMIT 1"
    );

    if (mysqli_num_rows($result) == 1) {
        $edit_food = mysqli_fetch_assoc($result);
    }
}

/* CATEGORIES */
$categories = mysqli_query(
    $conn,
    "SELECT * FROM categories
     WHERE status=1
     ORDER BY name"
);

/* FOODS */
$foods = mysqli_query(
    $conn,
    "SELECT foods.*, categories.name AS category_name
     FROM foods
     LEFT JOIN categories
     ON foods.category_id=categories.id
     ORDER BY foods.id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Foods - FoodExpress</title>

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

/* MAIN */

.container {
    width: 90%;
    max-width: 1200px;
    margin: 40px auto;
}

.page-header {
    margin-bottom: 25px;
}

.page-header h1 {
    font-size: 32px;
    margin-bottom: 7px;
}

.page-header p {
    color: #777;
}

/* MESSAGE */

.message {
    background: #dcfce7;
    color: #166534;
    padding: 13px 18px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: bold;
}

/* FORM */

.form-card {
    background: white;
    padding: 28px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.07);
    margin-bottom: 40px;
}

.form-card h2 {
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 7px;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 7px;
    font-size: 14px;
    outline: none;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    border-color: #e63946;
}

.form-group textarea {
    min-height: 90px;
    resize: vertical;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.primary-btn {
    border: none;
    background: #e63946;
    color: white;
    padding: 12px 20px;
    border-radius: 7px;
    font-weight: bold;
    cursor: pointer;
}

.primary-btn:hover {
    background: #c92f3b;
}

.cancel-btn {
    text-decoration: none;
    background: #555;
    color: white;
    padding: 12px 20px;
    border-radius: 7px;
    font-weight: bold;
}

/* FOOD SECTION */

.food-section h2 {
    margin-bottom: 20px;
}

.food-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
}

/* FOOD CARD */

.food-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.07);
    transition: 0.3s;
}

.food-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

.food-image {
    width: 100%;
    height: 190px;
    background: #eee;
    overflow: hidden;
}

.food-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #888;
}

.food-content {
    padding: 20px;
}

.food-content h3 {
    font-size: 20px;
    margin-bottom: 8px;
}

.category {
    display: inline-block;
    background: #fff0ed;
    color: #e63946;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 12px;
}

.description {
    color: #666;
    font-size: 14px;
    min-height: 42px;
    margin-bottom: 12px;
}

.price {
    color: #e63946;
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 15px;
}

.image-name {
    color: #777;
    font-size: 12px;
    margin-bottom: 15px;
    word-break: break-all;
}

.card-actions {
    display: flex;
    gap: 8px;
}

.edit-btn,
.delete-btn {
    flex: 1;
    text-align: center;
    padding: 10px;
    border-radius: 7px;
    text-decoration: none;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

.edit-btn {
    background: #0d6efd;
}

.edit-btn:hover {
    background: #0b5ed7;
}

.delete-btn {
    background: #dc3545;
}

.delete-btn:hover {
    background: #bb2d3b;
}

/* EMPTY */

.empty-food {
    background: white;
    padding: 50px;
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

/* RESPONSIVE */

@media (max-width: 950px) {

    .food-grid {
        grid-template-columns: repeat(2, 1fr);
    }

}

@media (max-width: 650px) {

    .admin-navbar {
        flex-direction: column;
        gap: 15px;
    }

    .admin-nav {
        justify-content: center;
    }

    .container {
        width: 92%;
        margin-top: 30px;
    }

    .page-header h1 {
        font-size: 27px;
    }

    .food-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .primary-btn,
    .cancel-btn {
        text-align: center;
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

<a href="admin_orders.php">
Orders
</a>

<a href="admin_foods.php" class="active">
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

<div class="container">

<div class="page-header">

<h1>Food Management</h1>

<p>
Add, edit and manage all food items.
</p>

</div>


<?php if ($message != "") { ?>

<div class="message">

<?php echo htmlspecialchars($message); ?>

</div>

<?php } ?>


<!-- ADD / EDIT FORM -->

<div class="form-card">

<?php if ($edit_food) { ?>

<h2>Edit Food</h2>

<form method="POST">

<input
type="hidden"
name="food_id"
value="<?php echo $edit_food['id']; ?>"
>

<div class="form-group">

<label>Food Name</label>

<input
type="text"
name="name"
value="<?php echo htmlspecialchars($edit_food['name']); ?>"
required
>

</div>


<div class="form-group">

<label>Description</label>

<textarea
name="description"
required
><?php echo htmlspecialchars($edit_food['description']); ?></textarea>

</div>


<div class="form-group">

<label>Price</label>

<input
type="number"
name="price"
step="0.01"
value="<?php echo $edit_food['price']; ?>"
required
>

</div>


<div class="form-group">

<label>Category</label>

<select name="category_id" required>

<option value="">
Select Category
</option>

<?php while ($category = mysqli_fetch_assoc($categories)) { ?>

<option
value="<?php echo $category['id']; ?>"
<?php
if ($category['id'] == $edit_food['category_id']) {
    echo "selected";
}
?>
>

<?php echo htmlspecialchars($category['name']); ?>

</option>

<?php } ?>

</select>

</div>


<div class="form-group">

<label>Image File Name</label>

<input
type="text"
name="image"
value="<?php echo htmlspecialchars($edit_food['image']); ?>"
required
>

</div>


<div class="form-actions">

<button
type="submit"
name="update_food"
class="primary-btn"
>
Update Food
</button>

<a
href="admin_foods.php"
class="cancel-btn"
>
Cancel
</a>

</div>

</form>


<?php } else { ?>

<h2>Add New Food</h2>

<form method="POST">

<div class="form-group">

<label>Food Name</label>

<input
type="text"
name="name"
placeholder="Example: Paneer Pizza"
required
>

</div>


<div class="form-group">

<label>Description</label>

<textarea
name="description"
placeholder="Enter food description"
required
></textarea>

</div>


<div class="form-group">

<label>Price</label>

<input
type="number"
name="price"
step="0.01"
placeholder="Example: 199"
required
>

</div>


<div class="form-group">

<label>Category</label>

<select name="category_id" required>

<option value="">
Select Category
</option>

<?php while ($category = mysqli_fetch_assoc($categories)) { ?>

<option value="<?php echo $category['id']; ?>">

<?php echo htmlspecialchars($category['name']); ?>

</option>

<?php } ?>

</select>

</div>


<div class="form-group">

<label>Image File Name</label>

<input
type="text"
name="image"
placeholder="Example: pizza.jpg"
required
>

</div>


<div class="form-actions">

<button
type="submit"
name="add_food"
class="primary-btn"
>
Add Food
</button>

</div>

</form>

<?php } ?>

</div>


<!-- EXISTING FOODS -->

<div class="food-section">

<h2>Existing Food Items</h2>


<?php if (mysqli_num_rows($foods) == 0) { ?>

<div class="empty-food">
No food items found.
</div>

<?php } else { ?>


<div class="food-grid">


<?php while ($food = mysqli_fetch_assoc($foods)) { ?>


<div class="food-card">

<div class="food-image">

<?php

$image_path = "img/" . $food['image'];

if (!empty($food['image']) && file_exists($image_path)) {

?>

<img
src="<?php echo htmlspecialchars($image_path); ?>"
alt="<?php echo htmlspecialchars($food['name']); ?>"
>

<?php } else { ?>

<div class="no-image">
No Image
</div>

<?php } ?>

</div>


<div class="food-content">

<h3>
<?php echo htmlspecialchars($food['name']); ?>
</h3>


<div class="category">

<?php echo htmlspecialchars($food['category_name'] ?? 'Uncategorized'); ?>

</div>


<div class="description">

<?php echo htmlspecialchars($food['description']); ?>

</div>


<div class="price">

₹<?php echo number_format($food['price'], 2); ?>

</div>


<div class="image-name">

Image: <?php echo htmlspecialchars($food['image']); ?>

</div>


<div class="card-actions">

<a
href="admin_foods.php?edit=<?php echo $food['id']; ?>"
class="edit-btn"
>
Edit
</a>

<a
href="admin_foods.php?delete=<?php echo $food['id']; ?>"
class="delete-btn"
onclick="return confirm('Are you sure you want to delete this food?');"
>
Delete
</a>

</div>

</div>

</div>


<?php } ?>


</div>

<?php } ?>

</div>

</div>


<footer>

<p>
© 2026 FoodExpress | Online Food Ordering System
</p>

</footer>

</body>

</html>