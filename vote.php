<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user is regular user (not admin)
if ($_SESSION['role'] != 'user') {
    header("Location: admin/dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if user has already voted
$check_sql = "SELECT has_voted FROM users WHERE id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['has_voted'] == 1) {
    $_SESSION['error'] = "You have already voted!";
    header("Location: index.php");
    exit();
}

// Process vote
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['candidate_id'])) {
    $candidate_id = intval($_POST['candidate_id']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert vote
        $vote_sql = "INSERT INTO votes (user_id, candidate_id) VALUES (?, ?)";
        $stmt = $conn->prepare($vote_sql);
        $stmt->bind_param("ii", $user_id, $candidate_id);
        $stmt->execute();
        
        // Update candidate vote count
        $update_candidate_sql = "UPDATE candidates SET vote_count = vote_count + 1 WHERE id = ?";
        $stmt = $conn->prepare($update_candidate_sql);
        $stmt->bind_param("i", $candidate_id);
        $stmt->execute();
        
        // Mark user as voted
        $update_user_sql = "UPDATE users SET has_voted = 1 WHERE id = ?";
        $stmt = $conn->prepare($update_user_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['has_voted'] = 1;
        $_SESSION['success'] = "Your vote has been recorded successfully!";
        header("Location: index.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $_SESSION['error'] = "Failed to record vote. Please try again.";
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>