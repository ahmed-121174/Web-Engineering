<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// API CALL LIMIT CHECK
$today = date('Y-m-d');
$checkLimit = $conn->prepare("SELECT COUNT(*) as request_count FROM api_request_log WHERE user_id = ? AND DATE(request_time) = ?");
$checkLimit->bind_param("is", $user_id, $today);
$checkLimit->execute();
$result = $checkLimit->get_result()->fetch_assoc();

$limitReached = $result['request_count'] >= 5;

// CATEGORY TO SEARCH TERM MAPPING
$categoryQuery = [
    "GK" => "general knowledge",
    "CS" => "computer science",
    "Bio" => "biology"
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fetch Books</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<h2>Welcome <?= $_SESSION['username'] ?> | <a href="../logout.php">Logout</a></h2>
<h3>Fetch Books by Category</h3>

<form method="POST">
    <select name="category" required>
        <option value="">-- Select Category --</option>
        <option value="GK">GK</option>
        <option value="CS">CS</option>
        <option value="Bio">Bio</option>
    </select>
    <button type="submit" name="fetch" <?= $limitReached ? "disabled" : "" ?>>Fetch Books</button>
</form>

<?php
if (isset($_POST['fetch']) && !$limitReached) {
    $category = $_POST['category'];
    $query = $categoryQuery[$category];

    $url = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($query);
    $json = file_get_contents($url);
    $data = json_decode($json, true);

    echo "<h3>Books Fetched:</h3>";
    echo "<ul>";

    $insert = $conn->prepare("INSERT INTO books (title, author, category) VALUES (?, ?, ?)");
    $log = $conn->prepare("INSERT INTO api_request_log (user_id, category) VALUES (?, ?)");

    $inserted = 0;

    foreach ($data['items'] as $item) {
        $title = $item['volumeInfo']['title'] ?? 'Unknown Title';
        $authors = isset($item['volumeInfo']['authors']) ? implode(', ', $item['volumeInfo']['authors']) : 'Unknown Author';

        //insert to database
        $insert->bind_param("sss", $title, $authors, $category);
        $insert->execute();

        $inserted++;
        echo "<li><strong>$title</strong> by $authors</li>";

        if ($inserted >= 5) break; //limit how many books we store per request
    }

    //log request
    $log->bind_param("is", $user_id, $category);
    $log->execute();
}
elseif ($limitReached) {
    echo "<p style='color:red;'>API request limit (5 per day) reached for today.</p>";
}
?>
</body>
</html>
