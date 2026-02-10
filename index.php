<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user is admin
if ($_SESSION['role'] == 'admin') {
    header("Location: admin/dashboard.php");
    exit();
}

// Get all candidates
$candidates_sql = "SELECT * FROM candidates ORDER BY vote_count DESC";
$candidates_result = $conn->query($candidates_sql);

// Get total votes
$total_votes_sql = "SELECT COUNT(*) as total FROM votes";
$total_votes_result = $conn->query($total_votes_sql);
$total_votes = $total_votes_result->fetch_assoc()['total'];

// Get user's voting status
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT has_voted FROM users WHERE id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();
$has_voted = $user_data['has_voted'];
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election System - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Navigation Bar -->
        <div class="navbar">
            <h1>🗳️ Election System</h1>
            <div class="nav-links">
                <span class="user-info">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <!-- Success/Error Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php 
        echo $_SESSION['success']; 
        unset($_SESSION['success']);
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?php 
        echo $_SESSION['error']; 
        unset($_SESSION['error']);
        ?>
    </div>
<?php endif; ?>

        <!-- Voting Status Alert -->
        <?php if ($has_voted): ?>
            <div class="alert alert-info">
                ✅ You have already cast your vote! View the results below.
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                📢 You haven't voted yet. Choose your candidate below!
            </div>
        <?php endif; ?>

        <!-- Statistics Card -->
        <div class="card">
            <h3>Election Statistics</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $candidates_result->num_rows; ?></h3>
                    <p>Total Candidates</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $total_votes; ?></h3>
                    <p>Total Votes Cast</p>
                </div>
            </div>
        </div>

        <!-- Candidates Section -->
        <div class="card">
            <h3><?php echo $has_voted ? '📊 Election Results' : '🎯 Choose Your Candidate'; ?></h3>
            
            <?php if ($candidates_result->num_rows > 0): ?>
                <div class="candidates-grid">
                    <?php while($candidate = $candidates_result->fetch_assoc()): ?>
                        <div class="candidate-card">
                            <?php if ($candidate['photo']): ?>
                                <img src="assets/uploads/candidates/<?php echo htmlspecialchars($candidate['photo']); ?>" 
                                     alt="<?php echo htmlspecialchars($candidate['name']); ?>" 
                                     class="candidate-photo">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/120" alt="No Photo" class="candidate-photo">
                            <?php endif; ?>
                            
                            <h4><?php echo htmlspecialchars($candidate['name']); ?></h4>
                            <p class="party"><?php echo htmlspecialchars($candidate['party']); ?></p>
                            <p class="description"><?php echo htmlspecialchars($candidate['description']); ?></p>
                            
                            <?php if ($has_voted): ?>
                                <div class="vote-count">
                                    🗳️ <?php echo $candidate['vote_count']; ?> votes
                                </div>
                            <?php else: ?>
                                <form method="POST" action="vote.php" style="margin-top: 15px;">
                                    <input type="hidden" name="candidate_id" value="<?php echo $candidate['id']; ?>">
                                    <button type="submit" class="btn" onclick="return confirm('Are you sure you want to vote for <?php echo htmlspecialchars($candidate['name']); ?>?')">
                                        Vote Now
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 40px;">
                    No candidates available yet. Please check back later.
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>