<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ./signin.html');
    exit;
}

// Include database config
require_once '../config.php';

// Get owner from query string, default to current user
$owner = isset($_GET['owner']) ? trim($_GET['owner']) : $_SESSION['username'];

// Get profile user info
$query = "SELECT id, username, fullname, description, avatar FROM account WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $owner);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $conn->close();
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>User Not Found</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                text-align: center;
                padding: 50px;
                background: #f5f5f5;
            }
            .error-box {
                background: white;
                padding: 40px;
                border-radius: 10px;
                max-width: 500px;
                margin: 0 auto;
            }
            h1 { color: #d32f2f; }
            a { color: #667eea; text-decoration: none; margin-top: 20px; display: inline-block; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>❌ User Not Found</h1>
            <p>The user '<?php echo htmlspecialchars($owner); ?>' does not exist.</p>
            <a href="./index.php">← Back to Home</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$profile_user = $result->fetch_assoc();
$stmt->close();

$is_own_profile = ($_SESSION['username'] === $profile_user['username']);

// Handle image upload
$upload_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_own_profile) {
    if (isset($_FILES['post_image']) && $_FILES['post_image']['size'] > 0) {
        $file = $_FILES['post_image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 10 * 1024 * 1024; // 10MB

        if (!in_array($file['type'], $allowed_types)) {
            $upload_message = 'Only JPG, PNG, GIF files are allowed';
        } elseif ($file['size'] > $max_size) {
            $upload_message = 'File size must be less than 10MB';
        } else {
            // Generate unique filename
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'post_' . $profile_user['id'] . '_' . time() . '.' . $file_ext;
            $upload_path = '../uploads/posts/' . $new_filename;

            // Create posts folder if not exists
            if (!is_dir('../uploads/posts')) {
                mkdir('../uploads/posts', 0777, true);
            }

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Insert into database
                $insertQuery = "INSERT INTO posts (user_id, image) VALUES (?, ?)";
                $stmt = $conn->prepare($insertQuery);
                $image_path = 'uploads/posts/' . $new_filename;
                $stmt->bind_param("is", $profile_user['id'], $image_path);

                if ($stmt->execute()) {
                    $upload_message = 'Post uploaded successfully!';
                } else {
                    $upload_message = 'Error saving post to database';
                    unlink($upload_path);
                }
                $stmt->close();
            } else {
                $upload_message = 'Error uploading file';
            }
        }
    }
}

// Get user posts
$postsQuery = "SELECT id, image, created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($postsQuery);
$stmt->bind_param("i", $profile_user['id']);
$stmt->execute();
$posts_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile_user['fullname']); ?>'s Profile - Social Network</title>
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
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .profile-card {
            background: white;
            border-radius: 10px;
            padding: 50px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            margin: 0 auto 30px;
        }

        .profile-name {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .profile-username {
            font-size: 16px;
            color: #888;
            margin-bottom: 30px;
        }

        .profile-owner-label {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        .section-title {
            font-size: 14px;
            color: #667eea;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 15px;
            text-align: left;
        }

        .profile-description {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            color: #666;
            font-size: 15px;
            line-height: 1.8;
            text-align: left;
            min-height: 100px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-edit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-back {
            background: #f0f0f0;
            color: #333;
        }

        .btn-back:hover {
            background: #e0e0e0;
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

            .profile-card {
                padding: 30px 20px;
            }

            .profile-name {
                font-size: 24px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
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
                <a href="./profile.php" class="menu-link active">👤 Profile</a>
                <a href="./setting.php" class="menu-link">⚙️ Setting</a>
                <a href="./about.html" class="menu-link">ℹ️ About</a>
            </div>
            <div class="menu-right">
                <span class="user-info">Welcome, <strong><?php echo htmlspecialchars($_SESSION['fullname']); ?></strong></span>
                <a href="./signout.php" class="menu-link signout-link">🚪 Sign Out</a>
            </div>
        </div>
    </nav>

    <!-- Profile Content -->
    <div class="container">
        <div class="profile-card">

            <div class="profile-name"><?php echo htmlspecialchars($profile_user['fullname']); ?></div>
            <div class="profile-username">@<?php echo htmlspecialchars($profile_user['username']); ?></div>
            <div class="profile-avatar">
                <?php if ($profile_user['avatar']): ?>
                    <img src="../<?php echo htmlspecialchars($profile_user['avatar']); ?>"
                         style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                <?php else: ?>
                    👤
                <?php endif; ?>
            </div>
            <?php if ($is_own_profile): ?>
                <div class="profile-owner-label">Your Profile</div>
            <?php endif; ?>

            <div class="section-title">About This User</div>
            <div class="profile-description">
                <?php
                echo $profile_user['description'] ? htmlspecialchars($profile_user['description']) : '(No description yet)';
                ?>
            </div>

            <div class="action-buttons">
                <?php if ($is_own_profile): ?>
                    <a href="./setting.php" class="btn btn-edit">✏️ Edit Profile</a>
                <?php endif; ?>
                <a href="./index.php" class="btn btn-back">← Back to Home</a>
            </div>

            <?php if ($is_own_profile): ?>
                <!-- Upload Post Image Form -->
                <div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #eee;">
                    <h3 style="font-size: 18px; color: #667eea; margin-bottom: 20px;">📸 Upload Post Image</h3>
                    
                    <?php if (!empty($upload_message)): ?>
                        <div style="background: <?php echo strpos($upload_message, 'successfully') ? '#d4edda' : '#f8d7da'; ?>; 
                                    color: <?php echo strpos($upload_message, 'successfully') ? '#155724' : '#721c24'; ?>; 
                                    padding: 12px; border-radius: 5px; margin-bottom: 15px;">
                            <?php echo htmlspecialchars($upload_message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div style="margin-bottom: 15px;">
                            <input type="file" name="post_image" accept="image/*" required
                                   style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; width: 100%; box-sizing: border-box;">
                            <p style="font-size: 12px; color: #888; margin-top: 5px;">JPG, PNG, GIF (Max 10MB)</p>
                        </div>
                        <button type="submit" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                                      color: white; padding: 10px 20px; border: none; border-radius: 5px; 
                                                      cursor: pointer; font-weight: 600; width: 100%;">
                            📤 Post Image
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Gallery of Posts -->
            <?php if ($posts_result->num_rows > 0): ?>
                <div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #eee;">
                    <h3 style="font-size: 18px; color: #667eea; margin-bottom: 20px;">📷 Posts (<?php echo $posts_result->num_rows; ?>)</h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px;">
                        <?php 
                        $posts_result->data_seek(0);
                        while ($post = $posts_result->fetch_assoc()): 
                        ?>
                            <div style="position: relative; border-radius: 8px; cursor: pointer;
                                        box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s;"
                                 onmouseover="this.style.transform='scale(1.05)'" 
                                 onmouseout="this.style.transform='scale(1)'">
                                <img src="../<?php echo htmlspecialchars($post['image']); ?>" 
                                     style="width: 100%; max-width: 250px; height: auto; display: block;">
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.5); 
                                            color: white; padding: 8px; font-size: 12px;">
                                    <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
