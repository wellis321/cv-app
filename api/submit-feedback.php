<?php
/**
 * Submit User Feedback API Endpoint
 * Handles feedback submissions from the feedback widget
 */

require_once __DIR__ . '/../php/helpers.php';

header('Content-Type: application/json');

if (!isPost()) {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Verify CSRF token
$token = post(CSRF_TOKEN_NAME);
if (!verifyCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token. Please refresh the page and try again.']);
    exit;
}

// Rate limiting: max 5 submissions per hour per IP/user
$rateLimitKey = 'feedback_' . (isLoggedIn() ? 'user_' . getUserId() : 'ip_' . getClientIp());
$rateLimit = checkRateLimit($rateLimitKey, 5, 3600);

if (!$rateLimit['allowed']) {
    $minutesRemaining = ceil(($rateLimit['reset_at'] - time()) / 60);
    http_response_code(429);
    echo json_encode([
        'success' => false, 
        'error' => "Too many feedback submissions. Please try again in {$minutesRemaining} minute(s)."
    ]);
    exit;
}

// Get form data
$feedbackType = prepareForStorage(post('feedback_type', 'other'));
$message = prepareForStorage(post('message', ''));
$email = prepareForStorage(post('email', ''));
$senderName = prepareForStorage(post('name', ''));
$pageUrl = prepareForStorage(post('page_url', ''));
$userAgent = prepareForStorage($_SERVER['HTTP_USER_AGENT'] ?? '');

// Allow anonymous submissions when email is provided (e.g. contact form)
$userId = null;
if (isLoggedIn()) {
    $userId = getUserId();
    if (!$userId) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'User session invalid. Please log in again.']);
        exit;
    }
    // Get email from user profile if not provided in form
    if (empty($email)) {
        $user = getCurrentUser();
        $email = $user['email'] ?? null;
    }
} else {
    // Anonymous: require email
    if (empty($email)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Please provide your email address so we can respond.']);
        exit;
    }
}

// Validation
$errors = [];

if (empty($feedbackType)) {
    $errors[] = 'Feedback type is required';
} elseif (!in_array($feedbackType, ['bug', 'spelling', 'feature_request', 'personal_issue', 'other'])) {
    $errors[] = 'Invalid feedback type';
}

if (empty($message)) {
    $errors[] = 'Message is required';
} elseif (strlen($message) < 10) {
    $errors[] = 'Message must be at least 10 characters';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!validateEmail($email)) {
    $errors[] = 'Invalid email address';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => implode('. ', $errors)]);
    exit;
}

// Prepend sender name to message for contact form submissions
if (!empty($senderName)) {
    $message = "From: " . $senderName . "\n\n" . $message;
}

try {
    // Determine which column name to use (user_id or profile_id)
    $userIdColumn = 'user_id';
    try {
        $columns = db()->fetchAll("SHOW COLUMNS FROM user_feedback LIKE 'user_id'");
        if (empty($columns)) {
            $columns = db()->fetchAll("SHOW COLUMNS FROM user_feedback LIKE 'profile_id'");
            if (!empty($columns)) {
                $userIdColumn = 'profile_id';
            }
        }
    } catch (Exception $e) {
        // Default to user_id if check fails
        $userIdColumn = 'user_id';
    }
    
    // Check which columns exist in the table
    $allColumns = db()->fetchAll("SHOW COLUMNS FROM user_feedback");
    $columnNames = array_column($allColumns, 'Field');
    $hasEmail = in_array('email', $columnNames);
    $hasPageUrl = in_array('page_url', $columnNames);
    $hasUserAgent = in_array('user_agent', $columnNames);
    $hasBrowserInfo = in_array('browser_info', $columnNames);
    $hasStatus = in_array('status', $columnNames);
    $hasUpdatedAt = in_array('updated_at', $columnNames);
    
    // Get additional info from form if available
    $additionalInfoJson = post('additional_info', '');
    $additionalInfo = [];
    if (!empty($additionalInfoJson)) {
        $decoded = json_decode($additionalInfoJson, true);
        if (is_array($decoded)) {
            $additionalInfo = $decoded;
        }
    }
    
    // Parse browser info
    $browserInfo = [
        'user_agent' => $userAgent,
        'ip_address' => getClientIp(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Add additional info if available
    if (!empty($additionalInfo)) {
        $browserInfo['screen_resolution'] = ($additionalInfo['screenWidth'] ?? '') . 'x' . ($additionalInfo['screenHeight'] ?? '');
        $browserInfo['viewport_size'] = ($additionalInfo['viewportWidth'] ?? '') . 'x' . ($additionalInfo['viewportHeight'] ?? '');
        $browserInfo['device_pixel_ratio'] = $additionalInfo['devicePixelRatio'] ?? null;
        $browserInfo['timezone'] = $additionalInfo['timezone'] ?? null;
        $browserInfo['language'] = $additionalInfo['language'] ?? null;
        $browserInfo['platform'] = $additionalInfo['platform'] ?? null;
        $browserInfo['referrer'] = $additionalInfo['referrer'] ?? null;
        $browserInfo['cookie_enabled'] = $additionalInfo['cookieEnabled'] ?? null;
        $browserInfo['online'] = $additionalInfo['onLine'] ?? null;
    }
    
    // Try to extract browser details from user agent
    if (!empty($userAgent)) {
        // Simple browser detection (can be enhanced with a library)
        $browserInfo['detected_browser'] = 'Unknown';
        $browserInfo['detected_os'] = 'Unknown';
        $browserInfo['device_type'] = 'Desktop'; // Default
        
        // Detect device type
        if (preg_match('/(Mobile|Android|iPhone|iPad|iPod|BlackBerry|Windows Phone)/i', $userAgent)) {
            if (preg_match('/iPad/i', $userAgent)) {
                $browserInfo['device_type'] = 'Tablet';
            } elseif (preg_match('/(iPhone|iPod|Android)/i', $userAgent)) {
                $browserInfo['device_type'] = 'Mobile';
            } else {
                $browserInfo['device_type'] = 'Mobile';
            }
        }
        
        if (preg_match('/Chrome\/([0-9.]+)/i', $userAgent, $matches)) {
            $browserInfo['detected_browser'] = 'Chrome ' . $matches[1];
        } elseif (preg_match('/Firefox\/([0-9.]+)/i', $userAgent, $matches)) {
            $browserInfo['detected_browser'] = 'Firefox ' . $matches[1];
        } elseif (preg_match('/Safari\/([0-9.]+)/i', $userAgent, $matches) && !preg_match('/Chrome/i', $userAgent)) {
            $browserInfo['detected_browser'] = 'Safari ' . $matches[1];
        } elseif (preg_match('/Edge\/([0-9.]+)/i', $userAgent, $matches)) {
            $browserInfo['detected_browser'] = 'Edge ' . $matches[1];
        }
        
        if (preg_match('/Windows NT ([0-9.]+)/i', $userAgent, $matches)) {
            $browserInfo['detected_os'] = 'Windows ' . $matches[1];
        } elseif (preg_match('/Mac OS X ([0-9_]+)/i', $userAgent, $matches)) {
            $browserInfo['detected_os'] = 'macOS ' . str_replace('_', '.', $matches[1]);
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $browserInfo['detected_os'] = 'Linux';
        } elseif (preg_match('/Android ([0-9.]+)/i', $userAgent, $matches)) {
            $browserInfo['detected_os'] = 'Android ' . $matches[1];
        } elseif (preg_match('/iPhone OS ([0-9_]+)/i', $userAgent, $matches)) {
            $browserInfo['detected_os'] = 'iOS ' . str_replace('_', '.', $matches[1]);
        }
    }
    
    // Store feedback in database
    $feedbackId = generateUuid();
    
    // Build insert data based on available columns
    $insertData = [
        'id' => $feedbackId,
        $userIdColumn => $userId,
        'feedback_type' => $feedbackType,
        'message' => $message,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Add optional columns if they exist
    if ($hasEmail) {
        $insertData['email'] = $email;
    }
    if ($hasPageUrl) {
        // Always save page URL if available, otherwise use current page URL as fallback
        $insertData['page_url'] = !empty($pageUrl) ? $pageUrl : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
    }
    if ($hasUserAgent) {
        $insertData['user_agent'] = !empty($userAgent) ? $userAgent : null;
    }
    if ($hasBrowserInfo) {
        $insertData['browser_info'] = json_encode($browserInfo);
    }
    if ($hasStatus) {
        $insertData['status'] = 'new';
    }
    if ($hasUpdatedAt) {
        $insertData['updated_at'] = date('Y-m-d H:i:s');
    }
    
    db()->insert('user_feedback', $insertData);
    
    // Log feedback submission
    error_log("Feedback submitted: ID={$feedbackId}, Type={$feedbackType}, User=" . ($userId ?? 'anonymous') . ", Page=" . ($pageUrl ?? 'unknown'));
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your feedback! We appreciate you taking the time to help us improve.'
    ]);
} catch (Exception $e) {
    error_log("Feedback submission error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to submit feedback. Please try again later.'
    ]);
}
