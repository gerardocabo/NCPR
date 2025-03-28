<?php
session_start();
include 'conn.php';

if (isset($_POST['lastSeenId']) && isset($_SESSION['user'])) {
    $username = $_SESSION['user'];
    $lastSeenId = intval($_POST['lastSeenId']);

    $stmt = $conn->prepare("UPDATE users SET last_seen_id = ? WHERE username = ?");
    $stmt->bind_param("is", $lastSeenId, $username);
    if ($stmt->execute()) {
        echo "Last seen NCPR ID updated successfully.";
    } else {
        echo "Error updating last seen ID.";
    }
} else {
    echo "Invalid request.";
}
?>
