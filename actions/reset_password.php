<?php
/**
 * Reset Password — POST Handler
 * ------------------------------
 * Validates token, hashes new password, updates user.
 * No HTML — logic only, then redirect.
 */

require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo(BASE_URL . '/pages/login.php');
}

$token           = trim($_POST['token']            ?? '');
$password        = $_POST['password']               ?? '';
$confirmPassword = $_POST['confirm_password']        ?? '';

$resetData = fetchValidResetToken($token);

if (!$resetData) {
    redirectTo(BASE_URL . '/pages/reset_password.php?token=' . urlencode($token) . '&error=' . urlencode('This reset link is invalid or has expired.'));
}

if (!passwordMeetsRules($password)) {
    redirectTo(BASE_URL . '/pages/reset_password.php?token=' . urlencode($token) . '&error=' . urlencode('Password does not meet the required rules.'));
}

if ($password !== $confirmPassword) {
    redirectTo(BASE_URL . '/pages/reset_password.php?token=' . urlencode($token) . '&error=' . urlencode('Passwords do not match.'));
}

try {
    $pdo = db();
    $pdo->beginTransaction();

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Update user password
    $updateUser = $pdo->prepare("
        UPDATE users
        SET password_hash = :password_hash
        WHERE id = :user_id
    ");
    $updateUser->execute([
        'password_hash' => $passwordHash,
        'user_id'       => $resetData['user_id'],
    ]);

    // Mark this token as used
    $markUsed = $pdo->prepare("
        UPDATE password_reset_tokens
        SET used_at = NOW()
        WHERE id = :token_id
    ");
    $markUsed->execute([
        'token_id' => $resetData['token_id'],
    ]);

    // Expire all other unused tokens for this user
    $expireOthers = $pdo->prepare("
        UPDATE password_reset_tokens
        SET used_at = NOW()
        WHERE user_id = :user_id
          AND id <> :token_id
          AND used_at IS NULL
    ");
    $expireOthers->execute([
        'user_id'  => $resetData['user_id'],
        'token_id' => $resetData['token_id'],
    ]);

    $pdo->commit();

    redirectTo(BASE_URL . '/pages/login.php?success=' . urlencode('Password reset successful! Please sign in with your new password.'));
} catch (Exception $ex) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    redirectTo(BASE_URL . '/pages/reset_password.php?token=' . urlencode($token) . '&error=' . urlencode('Password reset failed. Please try again.'));
}
