<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

//cancel request if cancel ID passed
if (isset($_GET['cancel_id'])) {
    $cancel_id = intval($_GET['cancel_id']);
    $cancel = $conn->prepare("DELETE FROM book_requests WHERE request_id = ? AND user_id = ? AND status = 'pending'");
    $cancel->bind_param("ii", $cancel_id, $user_id);
    $cancel->execute();
    header("Location: dashboard.php");
    exit();
}

//fetch user request
$stmt = $conn->prepare("
    SELECT r.request_id, b.title, b.author, r.category, r.status, r.request_date
    FROM book_requests r
    JOIN books b ON r.book_id = b.book_id
    WHERE r.user_id = ?
    ORDER BY r.request_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<h2>Hello <?= $username ?> | <a href="../logout.php">Logout</a></h2>

<nav>
    <a href="fetch_books.php">Fetch New Books</a> | 
    <a href="request_book.php">Request Book</a>
</nav>

<h3>Your Book Requests</h3>

<table border="1" cellpadding="10">
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Category</th>
        <th>Status</th>
        <th>Requested At</th>
        <th>Action</th>
    </tr>
    <?php
    $hasCompleted = false;
    while ($row = $result->fetch_assoc()):
        if ($row['status'] === 'completed') $hasCompleted = true;
    ?>
        <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['author']) ?></td>
            <td><?= $row['category'] ?></td>
            <td><?= ucfirst($row['status']) ?></td>
            <td><?= $row['request_date'] ?></td>
            <td>
                <?php if ($row['status'] === 'pending'): ?>
                    <a href="?cancel_id=<?= $row['request_id'] ?>" onclick="return confirm('Cancel this request?')">Cancel</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php if ($hasCompleted): ?>
    <p style="color: green;"><strong> Notification:</strong> Your request for a book is now <em>completed</em>!</p>
<?php endif; ?>

</body>
</html>