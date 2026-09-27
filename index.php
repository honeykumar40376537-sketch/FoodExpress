<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "db.php";

/* SEARCH */
$search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');

/* CATEGORY FILTER */
$category_id = intval($_GET['category'] ?? 0);

/* CATEGORIES */
$categories = mysqli_query($conn, "
    SELECT * FROM categories
    WHERE status = 1
    ORDER BY name ASC
");

/* FOOD QUERY WITH REVIEW SUMMARY */
$sql = "
    SELECT 
        foods.*, 
        categories.name AS category_name,
        review_summary.avg_rating,
        review_summary.review_count
    FROM foods
    LEFT JOIN categories
        ON foods.category_id = categories.id
    LEFT JOIN (
        SELECT 
            food_id,
            AVG(rating) AS avg_rating,
            COUNT(*) AS review_count
        FROM reviews
        GROUP BY food_id
    ) AS review_summary
        ON foods.id = review_summary.food_id
    WHERE foods.availability = 1
";

if ($search != '') {
    $sql .= "
        AND (
            foods.name LIKE '%$search%'
            OR foods.description LIKE '%$search%'
        )
    ";
}

if ($category_id > 0) {
    $sql .= " AND foods.category_id = $category_id";
}

$sql .= " ORDER BY foods.id DESC";

$foods = mysqli_query($conn, $sql);


/* IMAGE MAPPING */
function getFoodImage($foodName)
{
    $name = strtolower(trim($foodName));

    $images = [
        "chocolate cake" => "chocolate-cake.jpg",
        "cold drink" => "cold-drink.jpg",
        "veg noodles" => "veg-noodles.jpg",
        "paneer momos" => "paneer-momos.jpg",
        "veg momos" => "veg-momos.jpg",
        "paneer biryani" => "paneer-biryani.jpg",
        "veg biryani" => "veg-biryani.jpg",
        "cheese burger" => "cheese-burger.jpg",
        "veg burger" => "veg-burger.jpg",
        "farmhouse pizza" => "farmhouse.jpg",
        "margherita pizza" => "margherita.jpg"
    ];

    if (isset($images[$name])) {
        return "img/" . $images[$name];
    }

    return "img/food.jpg";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Online Food Ordering System</title>

<link rel="stylesheet"
href="css/style.css">

<style>

.search-box {
    display:flex;
    max-width:600px;
    margin:25px auto;
    gap:10px;
}

.search-box input {
    flex:1;
    padding:13px;
    border:1px solid #ddd;
    border-radius:7px;
    font-size:16px;
}

.search-box button {
    padding:13px 25px;
    border:none;
    border-radius:7px;
    background:#e91e3c;
    color:white;
    font-size:16px;
    cursor:pointer;
}

.category {
    display:inline-block;
    padding:12px 20px;
    margin:5px;
    background:white;
    border-radius:7px;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
    text-decoration:none;
    color:#333;
    font-weight:bold;
}

.category:hover {
    background:#e91e3c;
    color:white;
}

.clear-filter {
    display:inline-block;
    padding:10px 18px;
    margin:5px;
    background:#333;
    color:white;
    text-decoration:none;
    border-radius:6px;
}

.review-button {
    display:inline-block;
    margin-top:10px;
    padding:10px 16px;
    background:#ff9800;
    color:white;
    text-decoration:none;
    border-radius:6px;
}

.view-review-button {
    display:inline-block;
    margin-top:10px;
    padding:10px 16px;
    background:#2196f3;
    color:white;
    text-decoration:none;
    border-radius:6px;
}

.rating-box {
    margin-top:8px;
    font-size:15px;
}

.rating-stars {
    color:#ff9800;
    font-size:20px;
    letter-spacing:1px;
}

.rating-number {
    font-weight:bold;
    color:#333;
}

.review-count {
    color:#777;
    margin-left:4px;
}

.no-rating {
    color:#888;
    font-size:14px;
    margin-top:8px;
}

.no-food {
    text-align:center;
    padding:30px;
    font-size:18px;
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

<a href="#categories">
Categories
</a>

<a href="#foods">
Menu
</a>

<?php if (isset($_SESSION['user_id'])) { ?>

<a href="my_orders.php">
My Orders
</a>

<span style="font-weight:bold;">
Hi,
<?php
echo htmlspecialchars($_SESSION['user_name']);
?>
</span>

<a href="logout.php">
Logout
</a>

<?php } else { ?>

<a href="login.php">
Login
</a>

<a href="register.php">
Register
</a>

<?php } ?>

<a href="cart.php">
Cart
</a>

</div>

</nav>


<!-- HERO -->

<section class="hero">

<h1>
Delicious Food, Delivered Fast
</h1>

<p>
Order your favourite food online and enjoy it at your doorstep.
</p>


<!-- SEARCH -->

<form
method="GET"
action="index.php"
class="search-box"
>

<input
type="text"
name="search"
placeholder="Search for food..."
value="<?php echo htmlspecialchars($search); ?>"
>

<button type="submit">
Search
</button>

</form>

</section>


<!-- CATEGORIES -->

<section
class="section"
id="categories"
>

<h2>
Food Categories
</h2>

<div class="categories">

<a
href="index.php"
class="category"
>
All
</a>

<?php while (
$category = mysqli_fetch_assoc($categories)
) { ?>

<a
href="index.php?category=<?php echo $category['id']; ?>"
class="category"
>

<?php
echo htmlspecialchars($category['name']);
?>

</a>

<?php } ?>


<?php if ($search != '' || $category_id > 0) { ?>

<a
href="index.php"
class="clear-filter"
>
Clear Filter
</a>

<?php } ?>

</div>

</section>


<!-- FOOD MENU -->

<section
class="section"
id="foods"
>

<h2>

<?php

if ($search != '') {

    echo "Search Results for: "
        . htmlspecialchars($search);

} elseif ($category_id > 0) {

    echo "Filtered Food";

} else {

    echo "Popular Food";

}

?>

</h2>


<div class="food-grid">

<?php

if (mysqli_num_rows($foods) == 0) {

?>

<div class="no-food">
No food found.
</div>

<?php

} else {

while ($food = mysqli_fetch_assoc($foods)) {

    $avg_rating = round(
        (float)($food['avg_rating'] ?? 0),
        1
    );

    $review_count = (int)($food['review_count'] ?? 0);

?>

<div class="food-card">

<!-- IMAGE -->

<div class="food-image">

<img
src="<?php echo getFoodImage($food['name']); ?>"
alt="<?php echo htmlspecialchars($food['name']); ?>"
onerror="this.src='img/food.jpg';"
>

</div>


<!-- NAME -->

<h3>

<?php
echo htmlspecialchars($food['name']);
?>

</h3>


<!-- CATEGORY -->

<p>

Category:

<strong>

<?php
echo htmlspecialchars($food['category_name']);
?>

</strong>

</p>


<!-- DESCRIPTION -->

<p>

<?php
echo htmlspecialchars($food['description']);
?>

</p>


<!-- RATING -->

<?php if ($review_count > 0) { ?>

<div class="rating-box">

<span class="rating-stars">

<?php

$full_stars = round($avg_rating);

for ($i = 1; $i <= 5; $i++) {

    echo ($i <= $full_stars) ? "★" : "☆";

}

?>

</span>

<span class="rating-number">
<?php echo $avg_rating; ?>/5
</span>

<span class="review-count">
(<?php echo $review_count; ?> reviews)
</span>

</div>

<?php } else { ?>

<div class="no-rating">
No reviews yet
</div>

<?php } ?>


<!-- PRICE -->

<div class="price">

₹<?php
echo number_format($food['price'], 2);
?>

</div>


<!-- ADD CART -->

<a
href="cart.php?add=<?php echo $food['id']; ?>"
class="add-cart"
>
Add to Cart
</a>


<!-- REVIEW BUTTON -->

<?php if (isset($_SESSION['user_id'])) { ?>

<a
href="review.php?food_id=<?php echo $food['id']; ?>"
class="review-button"
>
Write Review
</a>

<?php } else { ?>

<a
href="login.php"
class="review-button"
>
Login to Review
</a>

<?php } ?>


<!-- VIEW REVIEWS -->

<a
href="food_reviews.php?food_id=<?php echo $food['id']; ?>"
class="view-review-button"
>
View Reviews
</a>


</div>

<?php

}

}

?>

</div>

</section>


<!-- FOOTER -->

<footer>

<p>
© 2026 FoodExpress |
Online Food Ordering System
</p>

</footer>

</body>

</html>