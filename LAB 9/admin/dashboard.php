<?php
session_start();
include '../db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'];

// Stats
$total_users = $conn->query("SELECT COUNT(DISTINCT user_id) AS count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$total_requests = $conn->query("SELECT COUNT(*) AS count FROM book_requests")->fetch_assoc()['count'];
$in_progress = $conn->query("SELECT COUNT(*) AS count FROM book_requests WHERE status = 'pending'")->fetch_assoc()['count'];
$completed = $conn->query("SELECT COUNT(*) AS count FROM book_requests WHERE status = 'completed'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<h2>Welcome, <?= $admin_name ?> (Admin) | <a href="../logout.php">Logout</a></h2>

<h3> Dashboard Overview</h3>
<ul>
    <li><strong>Total Unique Users:</strong> <?= $total_users ?></li>
    <li><strong>Total Book Requests:</strong> <?= $total_requests ?></li>
    <li><strong>Requests in Progress:</strong> <?= $in_progress ?></li>
    <li><strong>Completed Requests:</strong> <?= $completed ?></li>
</ul>

</body>
</html>