<?php
require_once '../config/database.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $party = trim($_POST['party']);
    $description = trim($_POST['description']);
    
    // Validate inputs
    if (empty($name) || empty($party)) {
        $error = "Name and Party are required!";
    } else {
        $photo_name = '';
        
        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            $file_type = $_FILES['photo']['type'];
            $file_size = $_FILES['photo']['size'];
            
            if (!in_array($file_type, $allowed_types)) {
                $error = "Only JPG, PNG, and GIF files are allowed!";
            } elseif ($file_size > 5000000) { // 5MB
                $error = "File size must be less than 5MB!";
            } else {
                $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $photo_name = uniqid() . '_' . time() . '.' . $file_extension;
                $upload_path = '../assets/uploads/candidates/' . $photo_name;
                
                if (!move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                    $error = "Failed to upload photo!";
                    $photo_name = '';
                }
            }
        }
        
        // Insert candidate if no errors
        if (empty($error)) {
            $insert_sql = "INSERT INTO candidates (name, party, description, photo) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ssss", $name, $party, $description, $photo_name);
            
            if ($stmt->execute()) {
                $success = "Candidate added successfully!";
                // Clear form
                $_POST = array();
            } else {
                $error = "Failed to add candidate!";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Candidate - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Navigation -->
        <div class="navbar">
            <h1>➕ Add New Candidate</h1>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="../logout.php">Logout</a>
            </div>
        </div>

        <!-- Add Candidate Form -->
        <div class="form-box" style="max-width: 600px;">
            <h2>Candidate Information</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Candidate Name: *</label>
                    <input type="text" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Party Name: *</label>
                    <input type="text" name="party" required value="<?php echo isset($_POST['party']) ? htmlspecialchars($_POST['party']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" rows="4" placeholder="Brief description about the candidate..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Photo: (Optional)</label>
                    <input type="file" name="photo" accept="image/*">
                    <small style="color: #666; display: block; margin-top: 5px;">Max size: 5MB. Formats: JPG, PNG, GIF</small>
                </div>
                
                <button type="submit" class="btn">Add Candidate</button>
                <a href="dashboard.php" class="btn btn-secondary" style="display: inline-block; text-align: center; text-decoration: none; margin-top: 10px;">Back to Dashboard</a>
            </form>
        </div>
    </div>
</body>
</html>