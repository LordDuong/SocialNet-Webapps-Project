<?php
// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ./signin.html');
    exit;
}
?>

<nav class="menubar">
    <div class="menu-container">
        <div class="menu-left">
            <h2 class="logo">SocialNet</h2>
        </div>
        <div class="menu-center">
            <a href="./index.php" class="menu-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                🏠 Home
            </a>
            <a href="./profile.php" class="menu-link <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">
                👤 Profile
            </a>
            <a href="./setting.php" class="menu-link <?php echo (basename($_SERVER['PHP_SELF']) == 'setting.php') ? 'active' : ''; ?>">
                ⚙️ Setting
            </a>
            <a href="./about.php" class="menu-link <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>">
                ℹ️ About
            </a>
        </div>
        <div class="menu-right">
            <span class="user-info">Welcome, <strong><?php echo htmlspecialchars($_SESSION['fullname']); ?></strong></span>
            <a href="./signout.php" class="menu-link signout-link">🚪 Sign Out</a>
        </div>
    </div>
</nav>

<style>
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
    }
</style>
