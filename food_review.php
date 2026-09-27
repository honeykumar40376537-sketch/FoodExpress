<?php
session_start();
include "db.php";

$food_id = intval($_GET['food_id'] ?? 0);

if ($food_id <= 0) {
    header("Location: index.php");
    exit;
}

/* FOOD */
$food_result = mysqli_query(
    $conn,
    "SELECT * FROM foods WHERE id=$food_id LIMIT 1"
);

if (mysqli_num_rows($food_result) == 0) {
    die("Food not found.");
}

$food = mysqli_fetch_assoc($food_result);

/* REVIEWS */
$reviews = mysqli_query(
    $conn,
    "SELECT reviews.*, users.name
     FROM reviews
     LEFT JOIN users ON reviews.user_id = users.id
     WHERE reviews.food_id=$food_id
     ORDER BY reviews.id DESC"
);

/* RATING SUMMARY */
$summary = mysqli_query(
    $conn,
    "SELECT 
        AVG(rating) AS avg_rating,
        COUNT(*) AS review_count
     FROM reviews
     WHERE food_id=$food_id"
);

$rating_data = mysqli_fetch_assoc($summary);

$avg_rating = round(
    (float)($rating_data['avg_rating'] ?? 0),
    1
);

$review_count = (int)($rating_data['review_count'] ?? 0);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Reviews - <?php echo htmlspecialchars($food['name']); ?>
</title>

<link rel="stylesheet"
href="css/style.css">

<style>

.review-page {
    max-width:800px;
    margin:40px auto;
}

.food-title {
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    margin-bottom:25px;
}

.rating-summary {
    font-size:20px;
    margin-top:10px;
}

.stars {
    color:#ff9800;
    font-size:24px;
}

.review-card {
    background:white;
    padding:20px;
    margin-bottom:15px;
    border-radius:10px;
    box-shadow:0 3px 12px rgba(0,0,0,0.08);
}

.review-user {
    font-weight:bold;
    font-size:17px;
}

.review-comment {
    margin-top:10px;
    color:#555;
}

.review-date {
    color:#888;
    font-size:13px;
    margin-top:8px;
}

.write-review {
    display:inline-block;
    margin-top:15px;
    padding:11px 18px;
    background:#e91e3c;
    color:white;
    text-decoration:none;
    border-radius:6px;
}

.no-review {
    background:white;
    padding:25px;
    text-align:center;
    border-radius:10px;
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

<a href="cart.php">
Cart
</a>

<?php if (isset($_SESSION['user_id'])) { ?>

<a href="my_orders.php">
My Orders
</a>

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

</div>

</nav>


<!-- REVIEWS -->

<section class="section">

<div class="review-page">

<div class="food-title">

<h2>
<?php echo htmlspecialchars($food['name']); ?>
</h2>

<div class="rating-summary">

<?php if ($review_count > 0) { ?>

<span class="stars">
★★★★★
</span>

<strong>
<?php echo $avg_rating; ?>/5
</strong>

<span>
(<?php echo $review_count; ?> Reviews)
</span>

<?php } else { ?>

<span>
No reviews yet
</span>

<?php } ?>

</div>


<?php if (isset($_SESSION['user_id'])) { ?>

<a
href="review.php?food_id=<?php echo $food_id; ?>"
class="write-review"
>
Write a Review
</a>

<?php } ?>

</div>


<h2>
Customer Reviews
</h2>


<?php if (mysqli_num_rows($reviews) > 0) { ?>

<?php while ($review = mysqli_fetch_assoc($reviews)) { ?>

<div class="review-card">

<div class="review-user">

<?php
echo htmlspecialchars(
    $review['name'] ?? 'Customer'
);
?>

</div>


<div class="stars">

<?php

$rating = intval($review['rating']);

for ($i = 1; $i <= 5; $i++) {

    echo $i <= $rating ? "★" : "☆";

}

?>

</div>


<div class="review-comment">

<?php
echo htmlspecialchars($review['comment']);
?>

</div>


<div class="review-date">

<?php
echo date(
    "d M Y",
    strtotime($review['created_at'])
);
?>

</div>

</div>

<?php } ?>

<?php } else { ?>

<div class="no-review">

No reviews available for this food yet.

</div>

<?php } ?>

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