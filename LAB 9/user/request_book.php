<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];

// Fetch books for dropdown
$books = $conn->query("SELECT * FROM books");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'];
    $category = $_POST['category'];
    $file = $_FILES['optional_file'];

    $filename = null;

    if ($file['name']) {
        $uploadDir = "../uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = time() . "_" . basename($file['name']);
        $targetPath = $uploadDir . $filename;

        move_uploaded_file($file['tmp_name'], $targetPath);
    }

    $stmt = $conn->prepare("INSERT INTO book_requests (user_id, book_id, category, file_name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $book_id, $category, $filename);
    $stmt->execute();

    $success = "Your request has been submitted.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request a Book</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<h2>Welcome <?= $username ?> | <a href="../logout.php">Logout</a></h2>
<h3>Request a Book</h3>

<?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Username:</label>
    <input type="text" value="<?= $username ?>" disabled><br>

    <label>Email:</label>
    <input type="email" value="<?= $email ?>" disabled><br>

    <label>Select Book:</label>
    <select name="book_id" required>
        <option value="">-- Select Book --</option>
        <?php while($row = $books->fetch_assoc()): ?>
            <option value="<?= $row['book_id'] ?>"><?= $row['title'] ?> by <?= $row['author'] ?></option>
        <?php endwhile; ?>
    </select><br>

    <label>Category:</label>
    <select name="category" required>
        <option value="">-- Select Category --</option>
        <option value="GK">GK</option>
        <option value="CS">CS</option>
        <option value="Bio">Bio</option>
    </select><br>

    <label>Optional File (PDF/DOC):</label>
    <input type="file" name="optional_file"><br><br>

    <button type="submit">Submit Request</button>
</form>

<a href="dashboard.php">← Back to Dashboard</a>
</body>
</html>