<?php
/**
 * Email sending functions
 */

require_once __DIR__ . '/config.php';

/**
 * Send email using PHP mail() function
 * In production, consider using a service like SendGrid, Mailgun, or AWS SES
 */
function sendEmail($to, $subject, $message, $fromEmail = null, $fromName = null) {
    $fromEmail = $fromEmail ?: env('MAIL_FROM_EMAIL', 'noreply@' . parse_url(APP_URL, PHP_URL_HOST));
    $fromName = $fromName ?: env('MAIL_FROM_NAME', 'CV App');

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . $fromName . ' <' . $fromEmail . '>',
        'Reply-To: ' . $fromEmail,
        'X-Mailer: PHP/' . phpversion()
    ];

    $headersString = implode("\r\n", $headers);

    return mail($to, $subject, $message, $headersString);
}

/**
 * Send email verification email
 */
function sendVerificationEmail($email, $fullName, $verificationToken) {
    $verifyUrl = APP_URL . '/verify-email.php?token=' . urlencode($verificationToken);

    $subject = 'Verify your email address';

    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .button { display: inline-block; padding: 12px 24px; background-color: #2563eb; color: #ffffff; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .button:hover { background-color: #1d4ed8; }
            .footer { margin-top: 30px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Welcome to CV App!</h1>
            <p>Hello ' . htmlspecialchars($fullName ?: 'there') . ',</p>
            <p>Thank you for registering with CV App. Please verify your email address by clicking the button below:</p>
            <p><a href="' . htmlspecialchars($verifyUrl) . '" class="button">Verify Email Address</a></p>
            <p>Or copy and paste this link into your browser:</p>
            <p style="word-break: break-all; color: #2563eb;">' . htmlspecialchars($verifyUrl) . '</p>
            <p>This link will expire in 24 hours.</p>
            <p>If you did not create an account, please ignore this email.</p>
            <div class="footer">
                <p>Best regards,<br>The CV App Team</p>
            </div>
        </div>
    </body>
    </html>';

    return sendEmail($email, $subject, $message);
}

/**
 * Send password reset email
 */
function sendPasswordResetEmail($email, $fullName, $resetToken) {
    $resetUrl = APP_URL . '/reset-password.php?token=' . urlencode($resetToken);

    $subject = 'Reset your password';

    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .button { display: inline-block; padding: 12px 24px; background-color: #2563eb; color: #ffffff; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .button:hover { background-color: #1d4ed8; }
            .footer { margin-top: 30px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Password Reset Request</h1>
            <p>Hello ' . htmlspecialchars($fullName ?: 'there') . ',</p>
            <p>We received a request to reset your password. Click the button below to choose a new password:</p>
            <p><a href="' . htmlspecialchars($resetUrl) . '" class="button">Reset Password</a></p>
            <p>If the button does not work, copy and paste this link into your browser:</p>
            <p style="word-break: break-all; color: #2563eb;">' . htmlspecialchars($resetUrl) . '</p>
            <p>This link will expire in 1 hour. If you did not request a password reset, you can safely ignore this email.</p>
            <div class="footer">
                <p>Best regards,<br>The CV App Team</p>
            </div>
        </div>
    </body>
    </html>';

    return sendEmail($email, $subject, $message);
}

/**
 * Send username reminder email
 */
function sendUsernameReminderEmail($email, $fullName, $username) {
    $subject = 'Your CV App username';

    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .footer { margin-top: 30px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Your CV App Username</h1>
            <p>Hello ' . htmlspecialchars($fullName ?: 'there') . ',</p>
            <p>You recently asked for a reminder of your username. Here it is:</p>
            <p style="font-size: 20px; font-weight: bold; color: #2563eb;">' . htmlspecialchars($username) . '</p>
            <p>You can log in at <a href="' . htmlspecialchars(APP_URL) . '">' . htmlspecialchars(APP_URL) . '</a> using your email address and this username.</p>
            <p>If you did not request this email, you can ignore it.</p>
            <div class="footer">
                <p>Best regards,<br>The CV App Team</p>
            </div>
        </div>
    </body>
    </html>';

    return sendEmail($email, $subject, $message);
}
