<?php
/**
 * Shared Helper Functions
 * -----------------------
 * Used across all pages and actions.
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

/* ── Output helpers ───────────────────────────────── */

/**
 * HTML-escape a string for safe output.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/* ── Navigation helpers ───────────────────────────── */

/**
 * Redirect to a URL and stop execution.
 */
function redirectTo(string $url): void
{
    header("Location: $url");
    exit();
}

/* ── Session helpers ──────────────────────────────── */

/**
 * Start the session if not already started.
 */
function startAppSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/* ── Token / Invitation helpers ───────────────────── */

/**
 * Generate a cryptographically-secure invitation token.
 * Returns [plainToken, tokenHash].
 */
function generateInvitationToken(): array
{
    $plainToken = bin2hex(random_bytes(32));
    $tokenHash  = hash('sha256', $plainToken);

    return [$plainToken, $tokenHash];
}

/**
 * Check whether a password meets the strength rules.
 * At least 8 chars, 1 uppercase, 1 digit, 1 special character.
 */
function passwordMeetsRules(string $password): bool
{
    return (bool) preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password);
}

/**
 * Look up a valid (unexpired, unused) invitation by plain token.
 * Returns the row or null.
 */
function fetchValidInvitationByToken(string $plainToken): ?array
{
    if ($plainToken === '') {
        return null;
    }

    $tokenHash = hash('sha256', $plainToken);

    $sql = "
        SELECT
            it.id         AS token_id,
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

/**
 * Mark ALL unused tokens for a user as used (invalidate them).
 */
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

/**
 * Append an invitation link to the local log file (for testing).
 */
function saveInviteLinkToLog(string $name, string $email, string $link, string $expiresAt): void
{
    $logPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'invite_log.txt';

    $content  = "------------------------------------------" . PHP_EOL;
    $content .= "Name: "            . $name              . PHP_EOL;
    $content .= "Email: "           . $email             . PHP_EOL;
    $content .= "Expires At: "      . $expiresAt         . PHP_EOL;
    $content .= "Invitation Link: " . $link              . PHP_EOL;
    $content .= "Generated At: "    . date('Y-m-d H:i:s') . PHP_EOL;

    file_put_contents($logPath, $content, FILE_APPEND);
}

/**
 * Send an invitation email (only if MAIL_ENABLED is true).
 */
function sendInvitationEmail(string $name, string $email, string $link, string $expiresAt): bool
{
    if (!MAIL_ENABLED) {
        return false;
    }

    $subject = "Welcome to ABC Gas Station - Activate Your Account";

    $message  = "Hello {$name},\n\n";
    $message .= "Your employee account has been created by Admin.\n";
    $message .= "Please use the secure link below to set your password:\n\n";
    $message .= $link . "\n\n";
    $message .= "This link will expire on: {$expiresAt}\n";
    $message .= "If you did not expect this message, please contact Admin.\n";

    $headers  = "From: no-reply@abcgas.com\r\n";
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

/* ── Password Reset helpers ───────────────────── */

/**
 * Generate a cryptographically-secure reset token.
 * Returns [plainToken, tokenHash].
 */
function generateResetToken(): array
{
    $plainToken = bin2hex(random_bytes(32));
    $tokenHash  = hash('sha256', $plainToken);

    return [$plainToken, $tokenHash];
}

/**
 * Look up a valid (unexpired, unused) password reset token.
 * Returns the row or null.
 */
function fetchValidResetToken(string $plainToken): ?array
{
    if ($plainToken === '') {
        return null;
    }

    $tokenHash = hash('sha256', $plainToken);

    $sql = "
        SELECT
            prt.id        AS token_id,
            prt.user_id,
            prt.expires_at,
            u.full_name,
            u.email,
            u.role,
            u.user_status
        FROM password_reset_tokens prt
        INNER JOIN users u ON u.id = prt.user_id
        WHERE prt.token_hash = :token_hash
          AND prt.used_at IS NULL
          AND prt.expires_at >= NOW()
          AND u.user_status = 'Active'
        LIMIT 1
    ";

    $stmt = db()->prepare($sql);
    $stmt->execute(['token_hash' => $tokenHash]);
    $row = $stmt->fetch();

    return $row ?: null;
}

/**
 * Invalidate all unused reset tokens for a user.
 */
function invalidateAllResetTokensForUser(int $userId): void
{
    $stmt = db()->prepare("
        UPDATE password_reset_tokens
        SET used_at = NOW()
        WHERE user_id = :user_id
          AND used_at IS NULL
    ");
    $stmt->execute(['user_id' => $userId]);
}

/**
 * Append a password reset link to the log file (for testing).
 */
function saveResetLinkToLog(string $email, string $link, string $expiresAt): void
{
    $logPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'invite_log.txt';

    $content  = "---------- PASSWORD RESET -----------------" . PHP_EOL;
    $content .= "Email: "        . $email              . PHP_EOL;
    $content .= "Expires At: "   . $expiresAt          . PHP_EOL;
    $content .= "Reset Link: "   . $link               . PHP_EOL;
    $content .= "Generated At: " . date('Y-m-d H:i:s') . PHP_EOL;

    file_put_contents($logPath, $content, FILE_APPEND);
}

/**
 * Send a password reset email (only if MAIL_ENABLED is true).
 */
function sendResetEmail(string $name, string $email, string $link, string $expiresAt): bool
{
    if (!MAIL_ENABLED) {
        return false;
    }

    $subject = "ABC Gas Station - Password Reset Request";

    $message  = "Hello {$name},\n\n";
    $message .= "We received a request to reset your password.\n";
    $message .= "Please use the secure link below to set a new password:\n\n";
    $message .= $link . "\n\n";
    $message .= "This link will expire on: {$expiresAt}\n";
    $message .= "If you did not request this, you can safely ignore this email.\n";

    $headers  = "From: no-reply@abcgas.com\r\n";
    $headers .= "Reply-To: " . ADMIN_CONTACT_EMAIL . "\r\n";

    return mail($email, $subject, $message, $headers);
}

