<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../config.php';

function create_jwt($user) {
    $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = base64_encode(json_encode([
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'role' => $user['role'],
        'exp' => time() + 60 * 60 * 4 // 4 hours
    ]));

    // Correct HMAC signing
    $signature = hash_hmac('sha256', "$header.$payload", JWT_SECRET, true);
    $signature = base64_encode($signature);

    return "$header.$payload.$signature";
}

function verify_jwt($token) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    [$header, $payload, $signature] = $parts;

    // Correct expected signature
    $expected = base64_encode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));
    if (!hash_equals($expected, $signature)) return null;

    $data = json_decode(base64_decode($payload), true);
    if (!$data || $data['exp'] < time()) return null;

    return $data;
}

function get_auth_user() {
    $headers = getallheaders(); // fixed spelling
    if (!isset($headers['Authorization'])) return null;

    $auth = $headers['Authorization'];
    if (strpos($auth, 'Bearer ') !== 0) return null;

    $token = substr($auth, 7);
    return verify_jwt($token);
}

function handle_login() {
    $db = get_db();
    $input = json_decode(file_get_contents('php://input'), true);

    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid Credentials']);
        return;
    }

    $token = create_jwt($user);

    echo json_encode([
        'token' => $token,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ]
    ]);
}
