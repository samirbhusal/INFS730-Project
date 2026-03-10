<?php
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('login.php');
}

$token = trim($_POST['token'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

$invitation = fetchValidInvitationByToken($token);

if (!$invitation) {
    redirectTo('invalid_link.php');
}

if (!passwordMeetsRules($password)) {
    redirectTo('activate.php?token=' . urlencode($token) . '&error=' . urlencode('Password does not meet the required rules.'));
}

if ($password !== $confirmPassword) {
    redirectTo('activate.php?token=' . urlencode($token) . '&error=' . urlencode('Passwords do not match.'));
}

try {
    $pdo = db();
    $pdo->beginTransaction();

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $updateUser = $pdo->prepare("
        UPDATE users
        SET password_hash = :password_hash,
            user_status = 'Active'
        WHERE id = :user_id
    ");
    $updateUser->execute([
        'password_hash' => $passwordHash,
        'user_id' => $invitation['user_id']
    ]);

    $markTokenUsed = $pdo->prepare("
        UPDATE invitation_tokens
        SET used_at = NOW()
        WHERE id = :token_id
    ");
    $markTokenUsed->execute([
        'token_id' => $invitation['token_id']
    ]);

    $expireOtherTokens = $pdo->prepare("
        UPDATE invitation_tokens
        SET used_at = NOW()
        WHERE user_id = :user_id
          AND id <> :token_id
          AND used_at IS NULL
    ");
    $expireOtherTokens->execute([
        'user_id' => $invitation['user_id'],
        'token_id' => $invitation['token_id']
    ]);

    $pdo->commit();

    startAppSession();
    $_SESSION['user_id'] = $invitation['user_id'];
    $_SESSION['user_name'] = $invitation['full_name'];
    $_SESSION['user_email'] = $invitation['email'];
    $_SESSION['role'] = 'employee';

    redirectTo('employee_dashboard.php?activated=1');
} catch (Exception $ex) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    redirectTo('activate.php?token=' . urlencode($token) . '&error=' . urlencode('Account activation failed. Please try again.'));
}