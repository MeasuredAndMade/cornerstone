<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../routes/auth.php'; // for get_auth_user()

// ------------------------------------------------------------
// GET /categories  → list all categories
// ------------------------------------------------------------
function list_categories() {
    $db = get_db();

    $stmt = $db->query("
        SELECT id, name
        FROM categories
        ORDER BY name ASC
    ");

    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($categories);
}



// ------------------------------------------------------------
// POST /categories  → create a category (admin‑only)
// ------------------------------------------------------------
function create_category() {
    $db = get_db();

    // 1. Authenticate user
    $currentUser = get_auth_user();
    if (!$currentUser) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    // 2. Only admins can create categories
    if ($currentUser['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Only admins can create categories']);
        return;
    }

    // 3. Parse request body
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Category name is required']);
        return;
    }

    // 4. Insert category
    $stmt = $db->prepare("
        INSERT INTO categories (name)
        VALUES (:name)
    ");

    $stmt->execute([
        ':name' => $data['name']
    ]);

    echo json_encode([
        'message' => 'Category created successfully',
        'category_id' => $db->lastInsertId()
    ]);
}
