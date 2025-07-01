<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: login.php");
    exit();
}

$users = $conn->query("SELECT * FROM users WHERE role = 'user'");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Users</title></head>
<body>
<h2>Manage Users</h2>
<a href="dashboard.php">Back to Dashboard</a> | <a href="logout.php">Logout</a>
<table border="1" cellpadding="8">
<tr><th>ID</th><th>Username</th><th>Email</th><th>Actions</th></tr>
<?php while ($u = $users->fetch_assoc()): ?>
<tr>
  <td><?= $u['user_id'] ?></td>
  <td><?= $u['username'] ?></td>
  <td><?= $u['email'] ?></td>
  <td>
    <form action="reset_password.php" method="POST" style="display:inline">
        <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
        <button type="submit">Reset Password</button>
    </form>
    <form action="delete_user.php" method="POST" style="display:inline" onsubmit="return confirm('Are you sure?')">
        <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
        <button type="submit">Delete</button>
    </form>
  </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>