<?php
/**
 * API Key Encryption Utilities
 * Handles encryption and decryption of sensitive API keys before database storage
 */

/**
 * Encrypt API key before storing in database
 * @param string $plaintext The plaintext API key
 * @return string|false The encrypted key (base64 encoded), or false on failure
 */
function encryptApiKey($plaintext) {
    if (empty($plaintext)) {
        return null;
    }
    
    // Get encryption key from environment
    $key = getEncryptionKey();
    if (!$key) {
        error_log("Encryption key not configured. API key encryption disabled.");
        return false;
    }
    
    // Use OpenSSL for encryption (more widely available than sodium)
    $method = 'AES-256-GCM';
    $ivLength = openssl_cipher_iv_length($method);
    
    if ($ivLength === false) {
        error_log("OpenSSL cipher method not available: " . $method);
        return false;
    }
    
    // Generate random IV
    $iv = openssl_random_pseudo_bytes($ivLength);
    if ($iv === false) {
        error_log("Failed to generate encryption IV");
        return false;
    }
    
    // Encrypt with authentication tag (GCM mode)
    $tag = '';
    $encrypted = openssl_encrypt(
        $plaintext,
        $method,
        $key,
        OPENSSL_RAW_DATA,
        $iv,
        $tag,
        '',
        16 // 16-byte authentication tag
    );
    
    if ($encrypted === false) {
        error_log("API key encryption failed: " . openssl_error_string());
        return false;
    }
    
    // Combine IV + tag + encrypted data, then base64 encode
    $combined = $iv . $tag . $encrypted;
    return base64_encode($combined);
}

/**
 * Decrypt API key from database
 * @param string $encrypted The encrypted API key (base64 encoded)
 * @return string|false The decrypted key, or false on failure
 */
function decryptApiKey($encrypted) {
    if (empty($encrypted)) {
        return null;
    }
    
    // Get encryption key from environment
    $key = getEncryptionKey();
    if (!$key) {
        error_log("Encryption key not configured. API key decryption disabled.");
        return false;
    }
    
    // Decode from base64
    $combined = base64_decode($encrypted, true);
    if ($combined === false) {
        error_log("Failed to decode encrypted API key from base64");
        return false;
    }
    
    $method = 'AES-256-GCM';
    $ivLength = openssl_cipher_iv_length($method);
    $tagLength = 16; // GCM tag length
    
    if ($ivLength === false || strlen($combined) < ($ivLength + $tagLength)) {
        error_log("Invalid encrypted API key format");
        return false;
    }
    
    // Extract IV, tag, and encrypted data
    $iv = substr($combined, 0, $ivLength);
    $tag = substr($combined, $ivLength, $tagLength);
    $encryptedData = substr($combined, $ivLength + $tagLength);
    
    // Decrypt with authentication
    $decrypted = openssl_decrypt(
        $encryptedData,
        $method,
        $key,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );
    
    if ($decrypted === false) {
        error_log("API key decryption failed: " . openssl_error_string());
        return false;
    }
    
    return $decrypted;
}

/**
 * Get encryption key from environment
 * Generates and stores key on first use if not present
 * @return string|false The encryption key, or false on failure
 */
function getEncryptionKey() {
    // Check if key is in environment
    $key = env('API_KEY_ENCRYPTION_KEY', '');
    
    if (!empty($key)) {
        return $key;
    }
    
    // If key doesn't exist, generate one and log it for manual addition to .env
    $newKey = generateEncryptionKey();
    if ($newKey) {
        error_log("Generated new encryption key. Please add this to your .env file:");
        error_log("API_KEY_ENCRYPTION_KEY=" . $newKey);
        return $newKey;
    }
    
    return false;
}

/**
 * Generate a new encryption key (one-time setup)
 * @return string|false The generated key (hex encoded), or false on failure
 */
function generateEncryptionKey() {
    // Generate 256-bit (32-byte) key for AES-256
    $keyBytes = openssl_random_pseudo_bytes(32);
    
    if ($keyBytes === false) {
        error_log("Failed to generate encryption key");
        return false;
    }
    
    // Return as hex string for easy storage in .env
    return bin2hex($keyBytes);
}

/**
 * Validate API key format (basic validation before encryption)
 * @param string $service The service name (openai, anthropic)
 * @param string $apiKey The API key to validate
 * @return bool True if format appears valid, false otherwise
 */
function validateApiKeyFormat($service, $apiKey) {
    if (empty($apiKey)) {
        return false;
    }
    
    switch (strtolower($service)) {
        case 'openai':
            // OpenAI keys typically start with 'sk-' and are 51+ characters
            return preg_match('/^sk-[a-zA-Z0-9]{48,}$/', $apiKey);
            
        case 'anthropic':
            // Anthropic keys start with 'sk-ant-' and are longer
            return preg_match('/^sk-ant-[a-zA-Z0-9\-_]{95,}$/', $apiKey);
            
        default:
            // Unknown service - just check minimum length
            return strlen($apiKey) >= 20;
    }
}

