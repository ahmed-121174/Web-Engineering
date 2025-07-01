<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['mark_complete'])) {
    $req_id = $_POST['req_id'];
    $conn->query("UPDATE book_requests SET status = 'completed', message = 'Your request has been completed.' WHERE request_id = $req_id");
}

if (isset($_POST['delete_request'])) {
    $req_id = $_POST['req_id'];
    $conn->query("DELETE FROM book_requests WHERE request_id = $req_id");
}

$requests = $conn->query("SELECT * FROM book_requests ORDER BY status DESC, request_id DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Book Requests</title></head>
<body>
<h2>Manage Book Requests</h2>
<a href="dashboard.php">Back to Dashboard</a> | <a href="logout.php">Logout</a>
<table border="1" cellpadding="8">
<tr><th>ID</th><th>User</th><th>Category</th><th>Status</th><th>Message</th><th>Actions</th></tr>
<?php while ($r = $requests->fetch_assoc()): ?>
<tr>
    <td><?= $r['request_id'] ?></td>
    <td><?= $r['username'] ?></td>
    <td><?= $r['category'] ?></td>
    <td><?= $r['status'] ?></td>
    <td><?= $r['message'] ?></td>
    <td>
        <?php if ($r['status'] !== 'completed'): ?>
        <form method="POST" style="display:inline">
            <input type="hidden" name="req_id" value="<?= $r['request_id'] ?>">
            <button type="submit" name="mark_complete">Mark Complete</button>
        </form>
        <?php endif; ?>
        <form method="POST" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this request?')">
            <input type="hidden" name="req_id" value="<?= $r['request_id'] ?>">
            <button type="submit" name="delete_request">Delete</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>