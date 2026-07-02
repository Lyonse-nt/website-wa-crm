<?php
/**
 * Wait for database connection before running migrations
 * Usage: php wait-for-db.php
 */

$maxAttempts = 30; // 30 attempts
$waitSeconds = 2;  // 2 seconds between attempts

echo "Waiting for database connection...\n";

for ($i = 1; $i <= $maxAttempts; $i++) {
    try {
        $host = getenv('DB_HOST') ?: 'mysql.railway.internal';
        $port = getenv('DB_PORT') ?: '3306';
        $database = getenv('DB_DATABASE') ?: 'railway';
        $username = getenv('DB_USERNAME') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';

        $dsn = "mysql:host=$host;port=$port;dbname=$database";
        
        echo "Attempt $i/$maxAttempts: Connecting to $host:$port...\n";
        
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        echo "✓ Database connection successful!\n";
        exit(0);
        
    } catch (PDOException $e) {
        echo "✗ Connection failed: " . $e->getMessage() . "\n";
        
        if ($i < $maxAttempts) {
            echo "Retrying in $waitSeconds seconds...\n";
            sleep($waitSeconds);
        }
    }
}

echo "✗ Failed to connect to database after $maxAttempts attempts\n";
exit(1);
