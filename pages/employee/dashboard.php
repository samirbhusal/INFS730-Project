<?php

/**
 * Employee Dashboard — Check-in / Check-out
 * ------------------------------------------
 * Protected page — requires employee login.
 * Displays live clock, check-in/out controls, and today's attendance summary.
 */

require_once __DIR__ . '/../../includes/auth_guard.php';
requireLogin('employee');

/* ── Shift configuration ────────────────────── */
define('SHIFT_START_HOUR', 9);
define('SHIFT_START_MINUTE', 0);
define('GRACE_MINUTES', 15);

/* ── Data ────────────────────────────────────── */
$userName = $_SESSION['user_name'] ?? 'Employee';
$userId = (int)$_SESSION['user_id'];
$attendance = getTodayAttendance($userId);
$isLate = isLateCheckIn(SHIFT_START_HOUR, SHIFT_START_MINUTE, GRACE_MINUTES);

// Determine current state
$hasCheckedIn = $attendance !== null;
$hasCheckedOut = $hasCheckedIn && $attendance['check_out'] !== null;

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';

// Format times for display
$checkInTime = $hasCheckedIn ? date('g:i A', strtotime($attendance['check_in'])) : null;
$checkOutTime = $hasCheckedOut ? date('g:i A', strtotime($attendance['check_out'])) : null;

// Build deadline time string for JS
$deadlineHour = SHIFT_START_HOUR;
$deadlineMinute = SHIFT_START_MINUTE + GRACE_MINUTES;
if ($deadlineMinute >= 60) {
    $deadlineHour += intdiv($deadlineMinute, 60);
    $deadlineMinute = $deadlineMinute % 60;
}
$deadlineFormatted = sprintf('%d:%02d AM', $deadlineHour, $deadlineMinute);

$pageTitle = 'Employee Dashboard';
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
            <a href="<?php echo BASE_URL; ?>/pages/employee/change_password.php" class="dropdown-item">
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
        <a href="<?php echo BASE_URL; ?>/pages/employee/dashboard.php" class="sidebar-item active">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" />
                <rect x="14" y="3" width="7" height="7" />
                <rect x="3" y="14" width="7" height="7" />
                <rect x="14" y="14" width="7" height="7" />
            </svg>
            <span>Dashboard</span>
        </a>
    </nav>
</aside>

<!-- ── Overlay (mobile sidebar backdrop) ────── -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ── Main Content ─────────────────────────── -->
<div class="dashboard-content" id="dashboardContent">
    <div class="page-wrapper">
        <div class="card attendance-card">

            <!-- ── Greeting + Live Clock ────────────── -->
            <div class="dashboard-greeting">
                <h1>Welcome,
                    <?php echo e($userName); ?>
                </h1>
                <p class="subtitle centered">
                    <?php echo date('l, F j, Y'); ?>
                </p>
                <div class="live-clock" id="liveClock">
                    <?php echo date('h:i:s A'); ?>
                </div>
            </div>

            <!-- ── Alerts ───────────────────────────── -->
            <?php if ($error): ?>
                <div class="alert error">
                    <?php echo e($error); ?>
                </div>
            <?php
            endif; ?>

            <!-- ── Action Area ──────────────────────── -->
            <div class="action-area">

                <?php if (!$hasCheckedIn): ?>
                    <!-- CHECK-IN FORM -->
                    <form id="checkInForm" class="attendance-form" action="<?php echo BASE_URL; ?>/actions/attendance.php"
                        method="post" novalidate>
                        <input type="hidden" name="action" value="checkin">

                        <div class="reason-field <?php echo $isLate ? 'show' : ''; ?>" id="reasonField">
                            <div class="late-notice">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <span>You are checking in after
                                    <?php echo e($deadlineFormatted); ?>. A reason is required.
                                </span>
                            </div>
                            <div class="checkin-row">
                                <div class="form-group">
                                    <label for="lateReason">Reason:</label>
                                    <textarea id="lateReason" name="late_reason" rows="3" placeholder="Reason..."
                                        <?php echo
                                        $isLate ? 'required' : ''; ?>></textarea>
                                    <small class="field-error" id="reasonError"></small>
                                </div>

                                <button type="submit" id="checkInBtn" class="action-btn checkin-btn">
                                    Check In
                                </button>
                            </div>
                        </div>
                    </form>

                <?php
                elseif ($hasCheckedIn && !$hasCheckedOut): ?>
                    <!-- CHECK-OUT FORM -->
                    <form id="checkOutForm" class="attendance-form" action="<?php echo BASE_URL; ?>/actions/attendance.php"
                        method="post">
                        <input type="hidden" name="action" value="checkout">

                        <div class="reason-field show" id="reasonField">
                            <?php if (!empty($attendance['late_reason'])): ?>
                                <div class="late-notice">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <circle cx="12" cy="12" r="10" />
                                        <line x1="12" y1="8" x2="12" y2="12" />
                                        <line x1="12" y1="16" x2="12.01" y2="16" />
                                    </svg>
                                    <span>You checked in late. Your reason has been recorded.</span>
                                </div>
                            <?php endif; ?>
                            <div class="checkin-row">
                                <div class="form-group">
                                    <label for="lateReasonDisplay">Reason:</label>
                                    <textarea id="lateReasonDisplay" name="late_reason_display" rows="3"
                                        placeholder="No late reason"
                                        readonly><?php echo e($attendance['late_reason'] ?? ''); ?></textarea>
                                </div>

                                <button type="submit" id="checkOutBtn" class="action-btn checkout-btn">
                                    Check Out
                                </button>
                            </div>
                        </div>
                    </form>

                <?php
                else: ?>
                    <!-- SHIFT COMPLETE -->
                    <div class="shift-complete-msg">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#067647" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <p>Your shift is complete for today. See you tomorrow!</p>
                    </div>
                <?php
                endif; ?>
            </div>

            <!-- ── Today's Activity Log ─────────────── -->
            <?php if ($hasCheckedIn): ?>
                <div class="attendance-log">
                    <h3>Today's Activity</h3>
                    <div class="log-entries">
                        <div class="log-entry">
                            <div class="log-icon checkin-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                            <div class="log-details">
                                <span class="log-label">Checked In</span>
                                <span class="log-time">
                                    <?php echo e($checkInTime); ?>
                                </span>
                            </div>
                        </div>

                        <?php if (!empty($attendance['late_reason'])): ?>
                            <div class="log-entry late-entry">
                                <div class="log-icon late-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <circle cx="12" cy="12" r="10" />
                                        <line x1="12" y1="8" x2="12" y2="12" />
                                        <line x1="12" y1="16" x2="12.01" y2="16" />
                                    </svg>
                                </div>
                                <div class="log-details">
                                    <span class="log-label">Late Reason</span>
                                    <span class="log-reason">
                                        <?php echo e($attendance['late_reason']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php
                        endif; ?>

                        <?php if ($hasCheckedOut): ?>
                            <div class="log-entry">
                                <div class="log-icon checkout-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                        <polyline points="16 17 21 12 16 7" />
                                        <line x1="21" y1="12" x2="9" y2="12" />
                                    </svg>
                                </div>
                                <div class="log-details">
                                    <span class="log-label">Checked Out</span>
                                    <span class="log-time">
                                        <?php echo e($checkOutTime); ?>
                                    </span>
                                </div>
                            </div>
                        <?php
                        endif; ?>
                    </div>
                </div>
            <?php
            endif; ?>

        </div>
    </div>
</div>

<?php
$footerScripts = [BASE_URL . '/public/js/dashboard.js'];
require_once __DIR__ . '/../../includes/footer.php';
?>