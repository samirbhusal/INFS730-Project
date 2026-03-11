<?php
require_once 'functions.php';

$message = '';
$error = '';
$generatedLink = '';
$mailInfo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($fullName === '') {
        $error = 'Employee name is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid employee email address.';
    } else {
        try {
            $pdo = db();
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT id, user_status FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $existingUser = $stmt->fetch();

            if ($existingUser && $existingUser['user_status'] === 'Active') {
                $pdo->rollBack();
                $error = 'This employee already has an active account.';
            } else {
                if ($existingUser) {
                    $userId = (int) $existingUser['id'];

                    $update = $pdo->prepare("
                        UPDATE users
                        SET full_name = :full_name,
                            role = 'employee',
                            user_status = 'Pending',
                            password_hash = NULL
                        WHERE id = :id
                    ");
                    $update->execute([
                        'full_name' => $fullName,
                        'id' => $userId
                    ]);

                    invalidateAllUnusedTokensForUser($userId);
                } else {
                    $insertUser = $pdo->prepare("
                        INSERT INTO users (full_name, email, role, user_status)
                        VALUES (:full_name, :email, 'employee', 'Pending')
                    ");
                    $insertUser->execute([
                        'full_name' => $fullName,
                        'email' => $email
                    ]);

                    $userId = (int) $pdo->lastInsertId();
                }

                [$plainToken, $tokenHash] = generateInvitationToken();
                $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

                $insertToken = $pdo->prepare("
                    INSERT INTO invitation_tokens (user_id, token_hash, expires_at)
                    VALUES (:user_id, :token_hash, :expires_at)
                ");
                $insertToken->execute([
                    'user_id' => $userId,
                    'token_hash' => $tokenHash,
                    'expires_at' => $expiresAt
                ]);

                $pdo->commit();

                $generatedLink = BASE_URL . '/activate.php?token=' . urlencode($plainToken);

                $mailSent = sendInvitationEmail($fullName, $email, $generatedLink, $expiresAt);
                saveInviteLinkToLog($fullName, $email, $generatedLink, $expiresAt);

                $message = 'Invitation created successfully.';
                $mailInfo = $mailSent
                    ? 'Email was sent successfully.'
                    : 'SMTP is not configured in XAMPP, so the invitation link was saved to invite_log.txt and is also shown below for testing.';
            }
        } catch (Exception $ex) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Something went wrong while creating the invitation: ' . $ex->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Employee</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-wrapper">
        <div class="card">
            <h1>Admin - Add Employee</h1>
            <p class="subtitle">Create a pending employee account and dispatch a secure activation link.</p>

            <?php if ($error): ?>
                <div class="alert error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert success"><?php echo e($message); ?></div>
            <?php endif; ?>

            <?php if ($mailInfo): ?>
                <div class="alert info"><?php echo e($mailInfo); ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="full_name">Employee Name</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>

                <div class="form-group">
                    <label for="email">Employee Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <button type="submit" class="primary-btn">Create Invitation</button>
            </form>

            <?php if ($generatedLink): ?>
                <div class="link-box">
                    <strong>Testing Link:</strong><br>
                    <a href="<?php echo e($generatedLink); ?>"><?php echo e($generatedLink); ?></a>
                </div>
            <?php endif; ?>

            <div class="small-links">
                <a href="login.php">Employee Login</a>
            </div>
        </div>
    </div>
</body>
</html>