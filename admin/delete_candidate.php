<?php
require_once '../config/database.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $candidate_id = intval($_GET['id']);
    
    // Get candidate photo to delete file
    $get_photo_sql = "SELECT photo FROM candidates WHERE id = ?";
    $stmt = $conn->prepare($get_photo_sql);
    $stmt->bind_param("i", $candidate_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $candidate = $result->fetch_assoc();
        
        // Delete photo file if exists
        if (!empty($candidate['photo'])) {
            $photo_path = '../assets/uploads/candidates/' . $candidate['photo'];
            if (file_exists($photo_path)) {
                unlink($photo_path);
            }
        }
        
        // Delete votes for this candidate
        $delete_votes_sql = "DELETE FROM votes WHERE candidate_id = ?";
        $stmt = $conn->prepare($delete_votes_sql);
        $stmt->bind_param("i", $candidate_id);
        $stmt->execute();
        
        // Delete candidate
        $delete_sql = "DELETE FROM candidates WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $candidate_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Candidate deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete candidate!";
        }
    }
    $stmt->close();
}

header("Location: dashboard.php");
exit();
?>