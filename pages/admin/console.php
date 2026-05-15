<?php
/**
 * Admin Console
 * -------------
 * Protected page — requires admin login.
 */

require_once __DIR__ . '/../../includes/auth_guard.php';
requireLogin('admin');

$userName = $_SESSION['user_name'] ?? 'Admin';

$pageTitle = 'Admin Console';
require_once __DIR__ . '/../../includes/header.php';

// Get user initials for profile avatar
$nameParts = explode(' ', $userName);
$initials = strtoupper(substr($nameParts[0], 0, 1));
if (count($nameParts) > 1) {
    $initials .= strtoupper(substr(end($nameParts), 0, 1));
}
?>

<!-- ── Top Header Bar ───────────────────────── -->
<header class="top-header" id="topHeader">
    <div class="header-left">
        <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
        <span class="header-title">ABC Gas Station</span>
    </div>
    <div class="header-right">
        <button class="profile-btn" id="profileBtn" aria-label="Profile menu">
            <span class="profile-avatar">
                <?php echo e($initials); ?>
            </span>
        </button>
        <!-- Profile Dropdown -->
        <div class="profile-dropdown" id="profileDropdown">
            <div class="dropdown-user-info">
                <span class="dropdown-name">
                    <?php echo e($userName); ?>
                </span>
                <span class="dropdown-email">
                    <?php echo e($_SESSION['user_email'] ?? ''); ?>
                </span>
            </div>
            <div class="dropdown-divider"></div>
            <a href="<?php echo BASE_URL; ?>/pages/admin/change_password.php" class="dropdown-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                Change Password
            </a>
            <a href="<?php echo BASE_URL; ?>/pages/logout.php" class="dropdown-item dropdown-item-danger">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
                Log Out
            </a>
        </div>
    </div>
</header>

<!-- ── Sidebar (expandable) ─────────────────── -->
<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <a href="<?php echo BASE_URL; ?>/pages/admin/console.php" class="sidebar-item active">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" />
                <rect x="14" y="3" width="7" height="7" />
                <rect x="3" y="14" width="7" height="7" />
                <rect x="14" y="14" width="7" height="7" />
            </svg>
            <span>Dashboard</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/pages/admin/add_user.php" class="sidebar-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="8.5" cy="7" r="4" />
                <line x1="20" y1="8" x2="20" y2="14" />
                <line x1="23" y1="11" x2="17" y2="11" />
            </svg>
            <span>Add Employee</span>
        </a>
    </nav>
</aside>

<!-- ── Overlay (mobile sidebar backdrop) ────── -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ── Main Content ─────────────────────────── -->
<div class="dashboard-content" id="dashboardContent">
    <div class="page-wrapper">
        <div class="card">
            <h1>Admin Console</h1>
            <p class="subtitle centered">Welcome, <?php echo e($userName); ?>.</p>
            <p class="subtitle centered">You have successfully signed in as an Admin.</p>

            <a href="<?php echo BASE_URL; ?>/pages/admin/add_user.php" class="primary-btn" style="margin-bottom: 12px;">Add Employee</a>
        </div>
    </div>
</div>

<?php
$footerScripts = [BASE_URL . '/public/js/dashboard.js'];
require_once __DIR__ . '/../../includes/footer.php';
?>
