<?php
require_once 'config/database.php';
require_once 'includes/auth-check.php';
require_login();

$user_id = $_SESSION['user_id'];
$errors = [];

// Fetch current user data
$stmt = $conn->prepare("SELECT full_name, email, phone, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize_input($_POST['full_name'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    
    // Validation
    if (empty($full_name)) {
        $errors['full_name'] = 'Full name is required';
    }
    
    // Handle file upload
    $profile_image = $user['profile_image']; // Keep existing if not changed
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        $file_type = $_FILES['profile_image']['type'];
        $file_size = $_FILES['profile_image']['size'];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors['profile_image'] = 'Only JPG, PNG, GIF, and WebP images are allowed';
        } elseif ($file_size > $max_size) {
            $errors['profile_image'] = 'Image size must be less than 2MB';
        } else {
            // Create uploads directory if it doesn't exist
            $upload_dir = 'assets/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate unique filename
            $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                // Delete old profile image if exists
                if (!empty($user['profile_image']) && file_exists($user['profile_image'])) {
                    unlink($user['profile_image']);
                }
                
                $profile_image = $upload_path;
                $_SESSION['profile_image'] = $upload_path;
            } else {
                $errors['profile_image'] = 'Failed to upload image';
            }
        }
    }
    
    // If no errors, update database
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, profile_image = ? WHERE id = ?");
        $stmt->bind_param("sssi", $full_name, $phone, $profile_image, $user_id);
        
        if ($stmt->execute()) {
            // Update session variables
            $_SESSION['user_name'] = $full_name;
            
            set_flash_message('success', 'Profile updated successfully!');
            header('Location: profile.php');
            exit();
        } else {
            $errors['general'] = 'Failed to update profile. Please try again.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | User Profile System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Edit Profile</h3>
                        <p class="mb-0">Update your information</p>
                    </div>
                    <div class="card-body">
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger">
                                <?= $errors['general'] ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" 
                                       class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
                                       id="full_name" 
                                       name="full_name" 
                                       value="<?= htmlspecialchars($full_name ?? $user['full_name']) ?>" 
                                       required>
                                <?php if (isset($errors['full_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errors['full_name'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       value="<?= htmlspecialchars($user['email']) ?>" 
                                       readonly>
                                <small class="text-muted">Email cannot be changed</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       value="<?= htmlspecialchars($phone ?? $user['phone']) ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="profile_image" class="form-label">Profile Image</label>
                                <input type="file" 
                                       class="form-control <?= isset($errors['profile_image']) ? 'is-invalid' : '' ?>" 
                                       id="profile_image" 
                                       name="profile_image" 
                                       accept="image/*">
                                <?php if (isset($errors['profile_image'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errors['profile_image'] ?>
                                    </div>
                                <?php endif; ?>
                                <small class="text-muted">Max 2MB. Allowed: JPG, PNG, GIF, WebP</small>
                                
                                <?php if (!empty($user['profile_image']) && file_exists($user['profile_image'])): ?>
                                    <div class="mt-2">
                                        <p class="mb-1">Current Image:</p>
                                        <img src="<?= htmlspecialchars($user['profile_image']) ?>" 
                                             alt="Current Profile" 
                                             style="max-width: 150px; height: auto; border-radius: 10px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="nav-buttons">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="profile.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>