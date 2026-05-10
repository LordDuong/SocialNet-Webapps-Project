<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ./signin.html');
    exit;
}

// Include database config
require_once '../config.php';

// Get current user info
$current_user_id = $_SESSION['user_id'];
$query = "SELECT id, username, fullname, description, avatar FROM account WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$current_user = $result->fetch_assoc();
$stmt->close();

// Get all users except current user
$all_users_query = "SELECT id, username, fullname, description, avatar FROM account WHERE id != ? ORDER BY fullname ASC";
$stmt = $conn->prepare($all_users_query);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$all_users = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Social Network</title>
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
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }

        .current-user-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 20px;
        }

        .user-name {
            font-size: 20px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 10px;
            color: #333;
        }

        .user-username {
            text-align: center;
            color: #888;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .user-section-title {
            font-size: 12px;
            color: #667eea;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .user-description {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            min-height: 80px;
        }

        .users-section h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .users-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .user-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            cursor: pointer;
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .user-card-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin-bottom: 15px;
        }

        .user-card-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .user-card-username {
            font-size: 12px;
            color: #888;
            margin-bottom: 10px;
        }

        .user-card-description {
            font-size: 13px;
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
            min-height: 50px;
        }

        .btn-view-profile {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-view-profile:hover {
            transform: scale(1.02);
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
        }

        .no-users {
            text-align: center;
            color: #888;
            padding: 40px;
            font-size: 16px;
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

            .main-content {
                grid-template-columns: 1fr;
            }

            .users-grid {
                grid-template-columns: 1fr;
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
                <a href="./index.php" class="menu-link active">🏠 Home</a>
                <a href="./profile.php" class="menu-link">👤 Profile</a>
                <a href="./setting.php" class="menu-link">⚙️ Setting</a>
                <a href="./about.html" class="menu-link">ℹ️ About</a>
            </div>
            <div class="menu-right">
                <span class="user-info">Welcome, <strong><?php echo htmlspecialchars($_SESSION['fullname']); ?></strong></span>
                <a href="./signout.php" class="menu-link signout-link">🚪 Sign Out</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="main-content">
            <!-- Current User Card -->
            <div class="current-user-card">
                <div class="user-name"><?php echo htmlspecialchars($current_user['fullname']); ?></div>
                <div class="user-username">@<?php echo htmlspecialchars($current_user['username']); ?></div>
                <div class="user-avatar">
 		    <?php if ($current_user['avatar']): ?>
    			<img src="../<?php echo htmlspecialchars($current_user['avatar']); ?>"
             		     style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
    		    <?php else: ?>
        		👤
    		    <?php endif; ?>
	    	</div>
                <div class="user-section-title">About</div>
                <div class="user-description">
                    <?php 
                    echo $current_user['description'] ? htmlspecialchars($current_user['description']) : '(No description yet)'; 
                    ?>
                </div>
            </div>

            <!-- All Users Section -->
            <div class="users-section">
                <h2>👥 Other Users</h2>
                <?php if ($all_users->num_rows > 0): ?>
                    <div class="users-grid">
                        <?php while ($user = $all_users->fetch_assoc()): ?>
                            <div class="user-card">
  				<div class="user-card-avatar">
				    <?php if ($user['avatar']): ?>
    					<img src="../<?php echo htmlspecialchars($user['avatar']); ?>"
             				     style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
    				    <?php else: ?>
        		    		👤
    				    <?php endif; ?>
		     		</div>
                                <div class="user-card-name"><?php echo htmlspecialchars($user['fullname']); ?></div>
                                <div class="user-card-username">@<?php echo htmlspecialchars($user['username']); ?></div>
                                <div class="user-card-description">
                                    <?php 
                                    echo $user['description'] ? htmlspecialchars($user['description']) : '(No description)'; 
                                    ?>
                                </div>
                                <a href="./profile.php?owner=<?php echo urlencode($user['username']); ?>">
                                    <button class="btn-view-profile">View Profile</button>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="no-users">
                        No other users in the system yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
