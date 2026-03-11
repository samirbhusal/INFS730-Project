<?php
session_start();

/*
    Demo users for testing.
    For a real system, these should come from a database
    and passwords should be hashed.
*/
$users = [
    [
        'email' => 'employee@test.com',
        'password' => 'employee123',
        'role' => 'employee',
        'name' => 'Employee User'
    ],
    [
        'email' => 'admin@test.com',
        'password' => 'admin123',
        'role' => 'admin',
        'name' => 'Admin User'
    ]
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = trim($_POST['role'] ?? 'employee');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: login.php?error=" . urlencode("Invalid email format.") . "&email=" . urlencode($email) . "&role=" . urlencode($role));
    exit();
}

if ($password === '') {
    header("Location: login.php?error=" . urlencode("Password is required.") . "&email=" . urlencode($email) . "&role=" . urlencode($role));
    exit();
}

$authenticatedUser = null;

foreach ($users as $user) {
    if (
        strtolower($user['email']) === strtolower($email) &&
        $user['password'] === $password &&
        $user['role'] === $role
    ) {
        $authenticatedUser = $user;
        break;
    }
}

if (!$authenticatedUser) {
    header("Location: login.php?error=" . urlencode("Incorrect email, password, or role selection.") . "&email=" . urlencode($email) . "&role=" . urlencode($role));
    exit();
}

session_regenerate_id(true);

$_SESSION['user_email'] = $authenticatedUser['email'];
$_SESSION['user_name'] = $authenticatedUser['name'];
$_SESSION['role'] = $authenticatedUser['role'];

if ($authenticatedUser['role'] === 'employee') {
    header("Location: employee_dashboard.php");
    exit();
}

if ($authenticatedUser['role'] === 'admin') {
    header("Location: admin_console.php");
    exit();
}

header("Location: login.php");
exit();