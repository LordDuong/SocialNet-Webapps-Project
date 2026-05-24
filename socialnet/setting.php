<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ./signin.html');
    exit;
}

// Include database config
require_once '../config.php';

$current_user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Get current user info
$query = "SELECT id, username, fullname, description FROM account WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$current_user = $result->fetch_assoc();
$stmt->close();

// Handle form submission
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $avatar = $current_user['avatar']; // Keep old avatar if no new upload

    // Validation
    if (empty($fullname)) {
        $message = 'Full name is required';
        $message_type = 'error';
    } else {
        // Handle avatar upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['size'] > 0) {
            $file = $_FILES['avatar'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB

            // Validation
            if (!in_array($file['type'], $allowed_types)) {
                $message = 'Only JPG, PNG, GIF files are allowed';
                $message_type = 'error';
            } elseif ($file['size'] > $max_size) {
                $message = 'File size must be less than 5MB';
                $message_type = 'error';
            } else {
                // Generate unique filename
                $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $new_filename = 'avatar_' . $current_user_id . '_' . time() . '.' . $file_ext;
                $upload_path = '../uploads/avatars/' . $new_filename;

                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // Delete old avatar if exists
                    if ($current_user['avatar'] && file_exists($current_user['avatar'])) {
                        unlink($current_user['avatar']);
                    }
                    $avatar = 'uploads/avatars/' . $new_filename;
                } else {
                    $message = 'Error uploading file';
                    $message_type = 'error';
                    $avatar = $current_user['avatar'];
                }
            }
        }

        // If no error from file upload
        if ($message_type !== 'error') {
            // Update user info
            $updateQuery = "UPDATE account SET fullname = '$fullname', description = '$description' WHERE id = $current_user_id";
            $result = $conn->query($updateQuery);

            if ($result) {
                // Update session
                $_SESSION['fullname'] = $fullname;
                $current_user['fullname'] = $fullname;
                $current_user['description'] = $description;
                $current_user['avatar'] = $avatar;
                
                $message = 'Profile updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error updating profile: ' . $conn->error;
                $message_type = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Social Network</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        .menubar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .menu-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
        }

        .menu-left {
            flex: 0 0 auto;
        }

        .logo {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .menu-center {
            flex: 1;
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 0 30px;
        }

        .menu-right {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .menu-link {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 5px;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .menu-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .menu-link.active {
            background: rgba(255, 255, 255, 0.3);
            border-bottom: 2px solid white;
        }

        .user-info {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }

        .signout-link {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 15px;
        }

        .signout-link:hover {
            background: rgba(255, 0, 0, 0.3);
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .setting-card {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .setting-title {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .setting-subtitle {
            color: #888;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .message {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            display: none;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: inherit;
        }

        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 150px;
        }

        .form-info {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-cancel {
            background: #f0f0f0;
            color: #333;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
        }

        .info-box {
            background: #f5f5f5;
            padding: 15px;
            border-left: 4px solid #667eea;
            border-radius: 5px;
            margin-bottom: 30px;
        }

        .info-box h3 {
            color: #667eea;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .info-box p {
            color: #666;
            font-size: 13px;
            margin: 0;
        }

        @media (max-width: 768px) {
            .menu-container {
                flex-wrap: wrap;
                gap: 10px;
            }

            .menu-left {
                flex: 1 1 100%;
                text-align: center;
            }

            .menu-center {
                flex: 1 1 100%;
                justify-content: space-around;
                margin: 0;
                gap: 10px;
                padding: 10px 0;
                border-top: 1px solid rgba(255, 255, 255, 0.2);
            }

            .menu-right {
                flex: 1 1 100%;
                justify-content: center;
                gap: 10px;
                padding-top: 10px;
                border-top: 1px solid rgba(255, 255, 255, 0.2);
            }

            .user-info {
                display: none;
            }

            .setting-card {
                padding: 20px;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Menu Bar -->
    <nav class="menubar">
        <div class="menu-container">
            <div class="menu-left">
                <h2 class="logo">SocialNet</h2>
            </div>
            <div class="menu-center">
                <a href="./index.php" class="menu-link">🏠 Home</a>
                <a href="./profile.php" class="menu-link">👤 Profile</a>
                <a href="./setting.php" class="menu-link active">⚙️ Setting</a>
                <a href="./about.html" class="menu-link">ℹ️ About</a>
            </div>
            <div class="menu-right">
                <span class="user-info">Welcome, <strong><?php echo htmlspecialchars($_SESSION['fullname']); ?></strong></span>
                <a href="./signout.php" class="menu-link signout-link">🚪 Sign Out</a>
            </div>
        </div>
    </nav>

    <!-- Settings Content -->
    <div class="container">
        <div class="setting-card">
            <h1 class="setting-title">⚙️ Profile Settings</h1>
            <p class="setting-subtitle">Edit your profile information</p>

            <div class="info-box">
                <h3>📌 Your Account</h3>
                <p>Username: <strong><?php echo htmlspecialchars($current_user['username']); ?></strong></p>
            </div>

            <?php if (!empty($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" required 
                           value="<?php echo htmlspecialchars($current_user['fullname']); ?>"
                           placeholder="Enter your full name">
                </div>
                <div class="form-group">
                    <label for="avatar">Avatar (Profile Picture)</label>
                    <div style="margin-bottom: 15px;">
                        <?php if ($current_user['avatar'] && file_exists($current_user['avatar'])): ?>
                            <img src="<?php echo htmlspecialchars($current_user['avatar']); ?>" 
                                 style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 10px;">
                            <p style="font-size: 12px; color: #888;">Current avatar</p>
                        <?php endif; ?>
                    </div>
                    <input type="file" id="avatar" name="avatar" accept="image/*" 
                           style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; width: 100%;">
                    <div class="form-info">💡 JPG, PNG, GIF (Max 5MB). Upload a new image to replace current avatar</div>
                </div>
                <div class="form-group">
                    <label for="description">About You</label>
                    <textarea id="description" name="description" 
                              placeholder="Tell others about yourself..."><?php echo htmlspecialchars($current_user['description']); ?></textarea>
                    <div class="form-info">💡 This will be displayed on your profile page</div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-save">💾 Save Changes</button>
                    <a href="./profile.php" style="display: flex; align-items: center; justify-content: center; text-decoration: none;">
                        <button type="button" class="btn-cancel">❌ Cancel</button>
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
