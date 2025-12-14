<?php
require_once 'config/database.php';
require_once 'includes/auth-check.php';
require_login();

// Get user data from session
$user_id = $_SESSION['user_id'];

// Fetch updated user data from database
$stmt = $conn->prepare("SELECT full_name, email, phone, profile_image, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | User Profile System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">My Profile</h3>
                        <p class="mb-0">Welcome, <?= htmlspecialchars($user['full_name']) ?></p>
                    </div>
                    <div class="card-body">
                        <?php
                        $flash_message = get_flash_message();
                        if ($flash_message): ?>
                            <div class="alert alert-<?= $flash_message['type'] ?>">
                                <?= $flash_message['message'] ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="profile-img-container">
                            <?php if (!empty($user['profile_image']) && file_exists($user['profile_image'])): ?>
                                <img src="<?= htmlspecialchars($user['profile_image']) ?>" 
                                     alt="Profile Image" 
                                     class="profile-img">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/150/667eea/ffffff?text=<?= substr($user['full_name'], 0, 1) ?>" 
                                     alt="Default Profile" 
                                     class="profile-img">
                            <?php endif; ?>
                        </div>
                        
                        <div class="profile-info">
                            <div class="info-item">
                                <div class="info-label">Full Name</div>
                                <div class="info-value"><?= htmlspecialchars($user['full_name']) ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Email Address</div>
                                <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Phone Number</div>
                                <div class="info-value"><?= !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Not set' ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Member Since</div>
                                <div class="info-value"><?= date('F j, Y', strtotime($user['created_at'])) ?></div>
                            </div>
                        </div>
                        
                        <div class="nav-buttons">
                            <a href="edit-profile.php" class="btn btn-primary">Edit Profile</a>
                            <a href="logout.php" class="btn btn-danger">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>