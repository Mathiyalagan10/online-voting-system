<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $candidate_id = $_POST['candidate_id'];

    $stmt = $conn->prepare("SELECT is_voted FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user['is_voted']) {
        $conn->query("INSERT INTO votes (user_id, candidate_id) VALUES ($user_id, $candidate_id)");
        $conn->query("UPDATE users SET is_voted = 1 WHERE id = $user_id");
        echo "Vote successfully cast!";
    } else {
        echo "You have already voted.";
    }
}

// Fetch candidates
$candidates = $conn->query("SELECT * FROM candidates");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voting Page</title>
</head>
<body>
    <h2>Vote for your candidate</h2>
    <form method="POST">
        <?php while ($candidate = $candidates->fetch_assoc()): ?>
            <input type="radio" name="candidate_id" value="<?= $candidate['id'] ?>" required>
            <?= $candidate['name'] ?><br>
        <?php endwhile; ?>
        <button type="submit">Vote</button>
    </form>
</body>
</html>
