-- =============================================
-- Attendance — Check-in / Check-out Tracking
-- =============================================
-- Run this file once after schema.sql to add attendance tracking.

USE project730_activation1;

CREATE TABLE IF NOT EXISTS attendance (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED NOT NULL,
    check_in      DATETIME     NOT NULL,
    check_out     DATETIME     NULL,
    late_reason   TEXT         NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_attendance_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
);
