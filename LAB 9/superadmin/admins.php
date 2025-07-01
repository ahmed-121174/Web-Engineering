<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
    $stmt->bind_param("sss", $username, $email, $password);
    $stmt->execute();
}

if (isset($_POST['delete_admin'])) {
    $id = $_POST['user_id'];
    $conn->query("DELETE FROM users WHERE user_id = $id AND role = 'admin'");
}

$admins = $conn->query("SELECT * FROM users WHERE role = 'admin'");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Admins</title></head>
<body>
<h2>Manage Admins</h2>
<a href="dashboard.php">Back to Dashboard</a> | <a href="logout.php">Logout</a>
<h3>Add New Admin</h3>
<form method="POST">
    Username: <input type="text" name="username" required>
    Email: <input type="email" name="email" required>
    Password: <input type="password" name="password" required>
    <button type="submit" name="add_admin">Add Admin</button>
</form>

<h3>All Admins</h3>
<table border="1" cellpadding="8">
<tr><th>ID</th><th>Username</th><th>Email</th><th>Action</th></tr>
<?php while ($a = $admins->fetch_assoc()): ?>
<tr>
    <td><?= $a['user_id'] ?></td>
    <td><?= $a['username'] ?></td>
    <td><?= $a['email'] ?></td>
    <td>
        <form method="POST" onsubmit="return confirm('Delete this admin?')">
            <input type="hidden" name="user_id" value="<?= $a['user_id'] ?>">
            <button type="submit" name="delete_admin">Delete</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>