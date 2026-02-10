<?php
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get statistics
$total_candidates_sql = "SELECT COUNT(*) as total FROM candidates";
$total_candidates = $conn->query($total_candidates_sql)->fetch_assoc()['total'];

$total_users_sql = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$total_users = $conn->query($total_users_sql)->fetch_assoc()['total'];

$total_votes_sql = "SELECT COUNT(*) as total FROM votes";
$total_votes = $conn->query($total_votes_sql)->fetch_assoc()['total'];

$voters_sql = "SELECT COUNT(*) as total FROM users WHERE has_voted = 1";
$voted_users = $conn->query($voters_sql)->fetch_assoc()['total'];

// Get all candidates
$candidates_sql = "SELECT * FROM candidates ORDER BY vote_count DESC";
$candidates_result = $conn->query($candidates_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Election System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Navigation -->
        <div class="navbar">
            <h1>🔐 Admin Dashboard</h1>
            <div class="nav-links">
                <span class="user-info">Admin: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="add_candidate.php">Add Candidate</a>
                <a href="../logout.php">Logout</a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $total_candidates; ?></h3>
                <p>Total Candidates</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $total_users; ?></h3>
                <p>Registered Voters</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $total_votes; ?></h3>
                <p>Total Votes</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $voted_users; ?></h3>
                <p>Voters Who Voted</p>
            </div>
        </div>

        <!-- Candidates Management -->
        <div class="card">
            <h3>📋 Manage Candidates</h3>
            
            <?php if ($candidates_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Party</th>
                            <th>Description</th>
                            <th>Votes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($candidate = $candidates_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $candidate['id']; ?></td>
                                <td>
                                    <?php if ($candidate['photo']): ?>
                                        <img src="../assets/uploads/candidates/<?php echo htmlspecialchars($candidate['photo']); ?>" 
                                             style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/50" style="width: 50px; height: 50px; border-radius: 50%;">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($candidate['name']); ?></td>
                                <td><?php echo htmlspecialchars($candidate['party']); ?></td>
                                <td><?php echo htmlspecialchars(substr($candidate['description'], 0, 50)) . '...'; ?></td>
                                <td><strong><?php echo $candidate['vote_count']; ?></strong></td>
                                <td>
                                    <a href="delete_candidate.php?id=<?php echo $candidate['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this candidate?')"
                                       style="color: #dc3545; text-decoration: none; font-weight: 600;">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 40px; color: #666;">
                    No candidates added yet. <a href="add_candidate.php">Add your first candidate</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>