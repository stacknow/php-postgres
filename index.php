<?php
// Database connection settings
$host = 'localhost';
$db = 'php_postgres_db';
$user = 'yourUsername';
$pass = 'yourPassword';

try {
    // Create a PDO connection
    $dsn = "pgsql:host=$host;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to get all users
function getUsers($pdo) {
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
}

// Function to create a new user
function createUser($pdo) {
    $input = json_decode(file_get_contents("php://input"), true);
    $name = $input['name'];
    $email = $input['email'];

    $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['name' => $name, 'email' => $email]);

    echo json_encode(['message' => 'User created', 'user' => ['name' => $name, 'email' => $email]]);
}

// Route the request based on the method and endpoint
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

header("Content-Type: application/json");

if ($method == 'GET' && $path == '/users') {
    getUsers($pdo);
} elseif ($method == 'POST' && $path == '/users') {
    createUser($pdo);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}
?>
