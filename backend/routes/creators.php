<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php'; // for get_auth_user()

// ------------------------------------------------------------
// GET /creators  → list all creators
// ------------------------------------------------------------
function get_creators() {
    $db = get_db();

    $stmt = $db->query("
        SELECT id, name, user_id
        FROM creators
        ORDER BY name ASC
    ");

    $creators = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($creators);
}



// ------------------------------------------------------------
// POST /creators  → create a creator (admin‑only)
// ------------------------------------------------------------
function create_creator() {
    $db = get_db();

    // 1. Authenticate user
    $currentUser = get_auth_user();
    if (!$currentUser) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    // 2. Only admins can create creators
    if ($currentUser['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Only admins can create creators']);
        return;
    }

    // 3. Parse request body
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Creator name is required']);
        return;
    }

    // 4. Insert creator
    $stmt = $db->prepare("
        INSERT INTO creators (name)
        VALUES (:name)
    ");

    $stmt->execute([
        ':name' => $data['name']
    ]);

    echo json_encode([
        'message' => 'Creator created successfully',
        'creator_id' => $db->lastInsertId()
    ]);
}
