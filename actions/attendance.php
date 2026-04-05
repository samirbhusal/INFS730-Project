<?php
/**
 * Attendance Action — Check-in / Check-out POST Handler
 * ------------------------------------------------------
 * Handles employee check-in and check-out actions.
 * No HTML — logic only, then redirect.
 */

require_once __DIR__ . '/../includes/functions.php';
startAppSession();

/* ── Constants ─────────────────────────────── */
define('SHIFT_START_HOUR',   9);
define('SHIFT_START_MINUTE', 0);
define('GRACE_MINUTES',      15);

$dashboardURL = BASE_URL . '/pages/employee/dashboard.php';

/* ── Guards ────────────────────────────────── */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo($dashboardURL);
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'employee') {
    redirectTo(BASE_URL . '/pages/login.php?error=' . urlencode('Please sign in first.'));
}

$action = trim($_POST['action'] ?? '');
$userId = (int) $_SESSION['user_id'];

/* ── Check-In ─────────────────────────────── */

if ($action === 'checkin') {

    // Prevent duplicate check-in
    $existing = getTodayAttendance($userId);
    if ($existing) {
        redirectTo($dashboardURL . '?error=' . urlencode('You have already checked in today.'));
    }

    // Late check-in — reason is required
    $isLate     = isLateCheckIn(SHIFT_START_HOUR, SHIFT_START_MINUTE, GRACE_MINUTES);
    $lateReason = trim($_POST['late_reason'] ?? '');

    if ($isLate && $lateReason === '') {
        redirectTo($dashboardURL . '?error=' . urlencode('A reason is required for late check-in.'));
    }

    $stmt = db()->prepare("
        INSERT INTO attendance (user_id, check_in, late_reason)
        VALUES (:user_id, NOW(), :late_reason)
    ");
    $stmt->execute([
        'user_id'     => $userId,
        'late_reason' => $isLate ? $lateReason : null,
    ]);

    redirectTo($dashboardURL . '?success=' . urlencode('Checked in successfully.'));
}

/* ── Check-Out ────────────────────────────── */

if ($action === 'checkout') {

    $existing = getTodayAttendance($userId);

    if (!$existing) {
        redirectTo($dashboardURL . '?error=' . urlencode('You must check in before checking out.'));
    }

    if ($existing['check_out'] !== null) {
        redirectTo($dashboardURL . '?error=' . urlencode('You have already checked out today.'));
    }

    $stmt = db()->prepare("
        UPDATE attendance
        SET check_out = NOW()
        WHERE id = :id
    ");
    $stmt->execute(['id' => $existing['id']]);

    redirectTo($dashboardURL . '?success=' . urlencode('Checked out successfully.'));
}

/* ── Fallback ─────────────────────────────── */

redirectTo($dashboardURL . '?error=' . urlencode('Invalid action.'));
