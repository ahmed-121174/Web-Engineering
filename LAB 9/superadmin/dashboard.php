<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: login.php");
    exit();
}


$name = $_SESSION['username'];

$stats = [
    'users' => $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetch_row()[0],
    'admins' => $conn->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetch_row()[0],
    'requests' => $conn->query("SELECT COUNT(*) FROM book_requests")->fetch_row()[0],
];
?>

<!DOCTYPE html>
<html>
<head><title>Super Admin Dashboard</title></head>
<body>
<h2>Welcome Super Admin, <?= $name ?> </h2>
<a href="logout.php">Logout</a>
<hr>

<h3> System Overview</h3>
<ul>
    <li>Total Users: <?= $stats['users'] ?></li>
    <li>Total Admins: <?= $stats['admins'] ?></li>
    <li>Total Book Requests: <?= $stats['requests'] ?></li>
</ul>

<h3> Manage</h3>
<ul>
    <li><a href="users.php">Manage Users</a></li>
    <li><a href="admins.php">Manage Admins</a></li>
    <li><a href="requests.php">Manage Book Requests</a></li>
</ul>
</body>
</html>