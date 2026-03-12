<?php
/**
 * Auth Action — Login POST Handler
 * ---------------------------------
 * Validates credentials against the database.
 * No HTML — logic only, then redirect.
 */

require_once __DIR__ . '/../includes/functions.php';
startAppSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo(BASE_URL . '/pages/login.php');
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
$role     = trim($_POST['role']     ?? 'employee');

// --- Validate role ---

$allowedRoles = ['employee', 'admin'];
if (!in_array($role, $allowedRoles, true)) {
    redirectTo(BASE_URL . '/pages/login.php?error=' . urlencode('Invalid role selected.'));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectTo(BASE_URL . '/pages/login.php?error=' . urlencode('Invalid email format.') . '&email=' . urlencode($email) . '&role=' . urlencode($role));
}

if ($password === '') {
    redirectTo(BASE_URL . '/pages/login.php?error=' . urlencode('Password is required.') . '&email=' . urlencode($email) . '&role=' . urlencode($role));
}

// --- Authenticate ---

$stmt = db()->prepare("
    SELECT id, full_name, email, password_hash, role, user_status
    FROM users
    WHERE email = :email
      AND role  = :role
    LIMIT 1
");
$stmt->execute(['email' => $email, 'role' => $role]);
$user = $stmt->fetch();

if (!$user || $user['user_status'] !== 'Active' || !password_verify($password, $user['password_hash'])) {
    redirectTo(BASE_URL . '/pages/login.php?error=' . urlencode('Incorrect email, password, or role selection.') . '&email=' . urlencode($email) . '&role=' . urlencode($role));
}

// --- Set Session ---

session_regenerate_id(true);

$_SESSION['user_id']    = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_name']  = $user['full_name'];
$_SESSION['role']       = $user['role'];

if ($user['role'] === 'employee') {
    redirectTo(BASE_URL . '/pages/employee/dashboard.php');
}

if ($user['role'] === 'admin') {
    redirectTo(BASE_URL . '/pages/admin/console.php');
}

redirectTo(BASE_URL . '/pages/login.php');
