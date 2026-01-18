<?php
/**
 * Database connection and helper functions
 */

require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        // For PHP < 8.5, use the init command option (deprecated in 8.5+)
        // PHP 8.4 and earlier: Use PDO::MYSQL_ATTR_INIT_COMMAND (not deprecated)
        // PHP 8.5+: Execute command after connection to avoid deprecation warning
        if (version_compare(PHP_VERSION, '8.5.0', '<')) {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci";
        }

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            // Set connection attributes after connection to handle MySQL 8.0 auth
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // For PHP 8.5+, execute charset command after connection (deprecated option removed)
            if (version_compare(PHP_VERSION, '8.5.0', '>=')) {
                $this->pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
            }
        } catch (PDOException $e) {
            // Ensure headers are set before outputting error
            if (!headers_sent()) {
                header('Content-Type: text/html; charset=UTF-8');
            }
            if (DEBUG) {
                die("Database connection failed: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
            } else {
                die("Database connection failed. Please try again later.");
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    /**
     * Execute a query and return results
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            if (DEBUG) {
                error_log("Database query error: " . $e->getMessage());
                error_log("SQL: " . $sql);
                error_log("Params: " . print_r($params, true));
            }
            throw $e;
        }
    }

    /**
     * Fetch a single row
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Fetch all rows
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Insert a record and return the last insert ID
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);

        return $this->pdo->lastInsertId();
    }

    /**
     * Update a record
     */
    public function update($table, $data, $where, $whereParams = []) {
        if (empty($data)) {
            return 0;
        }

        // Build SET clause with named parameters
        $setParts = [];
        foreach (array_keys($data) as $key) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);

        // Convert WHERE clause to use named parameters if it uses positional ones
        $whereParamsNamed = [];
        $whereProcessed = $where;

        // Check if WHERE uses positional parameters (?)
        if (strpos($where, '?') !== false) {
            // Replace ? with named parameters
            $whereParts = explode('?', $where);
            $paramIndex = 0;
            $whereProcessed = '';

            for ($i = 0; $i < count($whereParts); $i++) {
                $whereProcessed .= $whereParts[$i];
                if ($i < count($whereParts) - 1) {
                    // There's a parameter after this part
                    $paramName = ':where_param_' . $paramIndex;
                    $whereProcessed .= $paramName;
                    if (isset($whereParams[$paramIndex])) {
                        $whereParamsNamed[$paramName] = $whereParams[$paramIndex];
                    }
                    $paramIndex++;
                }
            }
        } else {
            // WHERE already uses named parameters, just merge them
            $whereParamsNamed = $whereParams;
        }

        $sql = "UPDATE {$table} SET {$setClause} WHERE {$whereProcessed}";
        $params = array_merge($data, $whereParamsNamed);

        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Delete a record
     */
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->pdo->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->pdo->rollback();
    }
}

// Convenience function to get database instance
function db() {
    return Database::getInstance();
}
