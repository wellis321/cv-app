<?php
/**
 * Create Super Admin Account
 * 
 * This script helps create a super admin account.
 * Run from command line: php scripts/create-super-admin.php
 * 
 * SECURITY WARNING: This script should be removed or secured after creating the initial super admin.
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/database.php';
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/utils.php';

// Only allow running from command line
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

echo "Super Admin Account Creator\n";
echo "==========================\n\n";

// Get email
echo "Enter email address: ";
$email = trim(fgets(STDIN));

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Error: Invalid email address.\n");
}

// Check if user already exists
$existing = db()->fetchOne("SELECT id, email, is_super_admin FROM profiles WHERE email = ?", [$email]);

if ($existing) {
    if (!empty($existing['is_super_admin'])) {
        die("Error: This user is already a super admin.\n");
    }
    
    echo "\nUser with this email already exists. Do you want to promote them to super admin? (y/n): ";
    $confirm = trim(fgets(STDIN));
    
    if (strtolower($confirm) !== 'y') {
        die("Cancelled.\n");
    }
    
    // Promote existing user
    try {
        db()->update('profiles', [
            'is_super_admin' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$existing['id']]);
        
        echo "\n✓ User promoted to super admin successfully!\n";
        echo "User ID: " . $existing['id'] . "\n";
        echo "Email: " . $existing['email'] . "\n\n";
        exit(0);
    } catch (Exception $e) {
        die("Error: " . $e->getMessage() . "\n");
    }
}

// Get full name
echo "Enter full name (optional): ";
$fullName = trim(fgets(STDIN));

// Get password
echo "Enter password: ";
$password = trim(fgets(STDIN));

if (strlen($password) < 8) {
    die("Error: Password must be at least 8 characters long.\n");
}

// Confirm password
echo "Confirm password: ";
$confirmPassword = trim(fgets(STDIN));

if ($password !== $confirmPassword) {
    die("Error: Passwords do not match.\n");
}

// Create user
try {
    $userId = generateUuid();
    $passwordHash = hashPassword($password);
    
    db()->insert('profiles', [
        'id' => $userId,
        'email' => $email,
        'password_hash' => $passwordHash,
        'full_name' => $fullName ?: null,
        'username' => 'admin' . substr(str_replace('-', '', $userId), 0, 8),
        'email_verified' => 1, // Auto-verify for super admin
        'is_super_admin' => 1,
        'account_type' => 'individual',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
    echo "\n✓ Super admin account created successfully!\n";
    echo "User ID: " . $userId . "\n";
    echo "Email: " . $email . "\n";
    echo "Username: admin" . substr(str_replace('-', '', $userId), 0, 8) . "\n\n";
    echo "You can now log in with this account.\n";
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}

