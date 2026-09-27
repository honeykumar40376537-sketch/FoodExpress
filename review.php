<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$food_id = intval($_GET['food_id'] ?? 0);

if ($food_id <= 0) {
    die("Invalid food.");
}

$food_result = mysqli_query(
    $conn,
    "SELECT * FROM foods WHERE id=$food_id LIMIT 1"
);

if (mysqli_num_rows($food_result) == 0) {
    die("Food not found.");
}

$food = mysqli_fetch_assoc($food_result);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string(
        $conn,
        $_POST['comment']
    );

    if ($rating < 1 || $rating > 5) {
        $message = "Please select a rating between 1 and 5.";
    } else {

        $check = mysqli_query(
            $conn,
            "SELECT id FROM reviews
             WHERE user_id=$user_id
             AND food_id=$food_id
             LIMIT 1"
        );

        if (mysqli_num_rows($check) > 0) {

            $message = "You have already reviewed this food.";

        } else {

            $query = mysqli_query(
                $conn,
                "INSERT INTO reviews
                (user_id, food_id, rating, comment)
                VALUES
                ($user_id, $food_id, $rating, '$comment')"
            );

            if ($query) {
                $message = "Review submitted successfully.";
            } else {
                $message = "Review submission failed.";
            }
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

<title>Write Review - FoodExpress</title>

<link rel="stylesheet"
href="css/style.css">

<style>

.review-box {
    max-width:600px;
    margin:40px auto;
    background:white;
    padding:30px;
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.review-box input,
.review-box textarea,
.review-box select {
    width:100%;
    padding:12px;
    margin:8px 0 15px;
    box-sizing:border-box;
    border:1px solid #ddd;
    border-radius:6px;
}

.review-box textarea {
    height:120px;
}

.review-button {
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
    color:#e91e3c;
    font-weight:bold;
}

</style>

</head>

<body>

<nav class="navbar">

<div class="logo">
FoodExpress
</div>

<div class="nav-links">

<a href="index.php">
Home
</a>

<a href="my_orders.php">
My Orders
</a>

<a href="cart.php">
Cart
</a>

<a href="logout.php">
Logout
</a>

</div>

</nav>

<section class="section">

<div class="review-box">

<h2>
Write a Review
</h2>

<h3>
<?php
echo htmlspecialchars($food['name']);
?>
</h3>

<?php if ($message != "") { ?>

<p class="message">
<?php
echo htmlspecialchars($message);
?>
</p>

<?php } ?>

<form method="POST">

<label>
Rating
</label>

<select name="rating" required>

<option value="">
Select Rating
</option>

<option value="5">
★★★★★ - 5 Stars
</option>

<option value="4">
★★★★☆ - 4 Stars
</option>

<option value="3">
★★★☆☆ - 3 Stars
</option>

<option value="2">
★★☆☆☆ - 2 Stars
</option>

<option value="1">
★☆☆☆☆ - 1 Star
</option>

</select>

<label>
Comment
</label>

<textarea
name="comment"
placeholder="Write your review..."
required
></textarea>

<button
type="submit"
class="review-button"
>
Submit Review
</button>

</form>

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