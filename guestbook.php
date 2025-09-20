<?php
session_start();

// Initialize guestbook array in session if not already set
if (!isset($_SESSION['guestbook'])) {
    $_SESSION['guestbook'] = [];
}

// Handle form submission (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['name'], $_POST['message'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $message = htmlspecialchars(trim($_POST['message']));

    if ($name !== "" && $message !== "") {
        $_SESSION['guestbook'][] = [
            'name' => $name,
            'message' => $message,
            'time' => date("Y-m-d H:i:s")
        ];
    }
}

// Handle filtering via GET
$filterUser = isset($_GET['user']) ? htmlspecialchars($_GET['user']) : "";
$messages = $_SESSION['guestbook'];
if ($filterUser !== "") {
    $messages = array_filter($messages, function ($entry) use ($filterUser) {
        return strtolower($entry['name']) === strtolower($filterUser);
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP Guestbook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <h1 class="mb-4">Guestbook</h1>

    <!-- Form (POST) -->
    <div class="card mb-4">
        <div class="card-header">Leave a Message</div>
        <div class="card-body">
            <form method="POST" action="guestbook.php">
                <div class="mb-3">
                    <label class="form-label">Your Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Message</button>
            </form>
        </div>
    </div>

    <!-- Filter form (GET) -->
    <div class="card mb-4">
        <div class="card-header">Filter Messages by User</div>
        <div class="card-body">
            <form method="GET" action="guestbook.php" class="d-flex">
                <input type="text" name="user" class="form-control me-2" placeholder="Enter name to filter" value="<?= $filterUser ?>">
                <button type="submit" class="btn btn-secondary">Filter</button>
            </form>
            <?php if ($filterUser): ?>
                <p class="mt-2">Showing messages from <strong><?= $filterUser ?></strong>. <a href="guestbook.php">Clear Filter</a></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Guestbook Messages -->
    <h2>Messages</h2>
    <?php if (count($messages) > 0): ?>
        <ul class="list-group">
            <?php foreach ($messages as $entry): ?>
                <li class="list-group-item">
                    <strong><?= $entry['name'] ?></strong> (<?= $entry['time'] ?>)<br>
                    <?= $entry['message'] ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No messages yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
