<?php
require_once 'config.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirectTo(string $url): void
{
    header("Location: $url");
    exit();
}

function startAppSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function generateInvitationToken(): array
{
    $plainToken = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $plainToken);

    return [$plainToken, $tokenHash];
}

function passwordMeetsRules(string $password): bool
{
    return (bool) preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password);
}

function fetchValidInvitationByToken(string $plainToken): ?array
{
    if ($plainToken === '') {
        return null;
    }

    $tokenHash = hash('sha256', $plainToken);

    $sql = "
        SELECT 
            it.id AS token_id,
            it.user_id,
            it.expires_at,
            it.used_at,
            u.full_name,
            u.email,
            u.role,
            u.user_status
        FROM invitation_tokens it
        INNER JOIN users u ON u.id = it.user_id
        WHERE it.token_hash = :token_hash
          AND it.used_at IS NULL
          AND it.expires_at >= NOW()
          AND u.user_status = 'Pending'
        LIMIT 1
    ";

    $stmt = db()->prepare($sql);
    $stmt->execute(['token_hash' => $tokenHash]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function invalidateAllUnusedTokensForUser(int $userId): void
{
    $stmt = db()->prepare("
        UPDATE invitation_tokens
        SET used_at = NOW()
        WHERE user_id = :user_id
          AND used_at IS NULL
    ");
    $stmt->execute(['user_id' => $userId]);
}

function saveInviteLinkToLog(string $name, string $email, string $link, string $expiresAt): void
{
    $logPath = __DIR__ . DIRECTORY_SEPARATOR . 'invite_log.txt';

    $content = "------------------------------------------" . PHP_EOL;
    $content .= "Name: " . $name . PHP_EOL;
    $content .= "Email: " . $email . PHP_EOL;
    $content .= "Expires At: " . $expiresAt . PHP_EOL;
    $content .= "Invitation Link: " . $link . PHP_EOL;
    $content .= "Generated At: " . date('Y-m-d H:i:s') . PHP_EOL;

    file_put_contents($logPath, $content, FILE_APPEND);
}

function sendInvitationEmail(string $name, string $email, string $link, string $expiresAt): bool
{
    if (!MAIL_ENABLED) {
        return false;
    }

    $subject = "Welcome to ABC Gas Station - Activate Your Account";

    $message = "Hello {$name},\n\n";
    $message .= "Your employee account has been created by Admin.\n";
    $message .= "Please use the secure link below to set your password:\n\n";
    $message .= $link . "\n\n";
    $message .= "This link will expire on: {$expiresAt}\n";
    $message .= "If you did not expect this message, please contact Admin.\n";

    $headers = "From: no-reply@abcgas.com\r\n";
    $headers .= "Reply-To: " . ADMIN_CONTACT_EMAIL . "\r\n";

    return mail($email, $subject, $message, $headers);
}

function requireEmployeeLogin(): void
{
    startAppSession();

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'employee') {
        redirectTo('login.php');
    }
}