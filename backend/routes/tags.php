<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../routes/auth.php'; // for get_auth_user()

// ------------------------------------------------------------
// GET /tags  → list all tags
// ------------------------------------------------------------
function list_tags() {
    $db = get_db();

    $stmt = $db->query("
        SELECT id, name
        FROM tags
        ORDER BY name ASC
    ");

    $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($tags);
}



// ------------------------------------------------------------
// POST /tags  → create a tag (admin‑only)
// ------------------------------------------------------------
function create_tag() {
    $db = get_db();

    // 1. Authenticate user
    $currentUser = get_auth_user();
    if (!$currentUser) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    // 2. Only admins can create tags
    if ($currentUser['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Only admins can create tags']);
        return;
    }

    // 3. Parse request body
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Tag name is required']);
        return;
    }

    // 4. Insert tag
    $stmt = $db->prepare("
        INSERT INTO tags (name)
        VALUES (:name)
    ");

    $stmt->execute([
        ':name' => $data['name']
    ]);

    echo json_encode([
        'message' => 'Tag created successfully',
        'tag_id' => $db->lastInsertId()
    ]);
}
