<?php
require_once __DIR__ . '/../db.php';

function generate_slug($string) {
    // Lowercase
    $slug = strtolower($string);

    // Replace non-alphanumeric with hyphens
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);

    // Trim hyphens
    $slug = trim($slug, '-');

    return $slug;
}

function ensure_unique_slug($db, $baseSlug) {
    $slug = $baseSlug;
    $counter = 1;

    while (true) {
        $stmt = $db->prepare("SELECT id FROM projects WHERE slug = ?");
        $stmt->execute([$slug]);

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        } else {
            return $slug;
        }
    }
}

function list_projects() {
    $db = get_db();

    // ------------------------------------------------------------
    // READ FILTERS FROM QUERY STRING
    // ------------------------------------------------------------
    $status   = $_GET['status']   ?? null;
    $category = $_GET['category'] ?? null;
    $tag      = $_GET['tag']      ?? null;
    $creator  = $_GET['creator']  ?? null;

    // Base query
    $sql = "
        SELECT 
            p.id,
            p.title,
            p.description,
            p.status,
            p.slug,
            p.created_at,
            p.updated_at,

            (
                SELECT image_url 
                FROM project_images 
                WHERE project_id = p.id 
                ORDER BY id ASC 
                LIMIT 1
            ) AS thumbnail

        FROM projects p
        WHERE 1 = 1
    ";

    $params = [];

    // ------------------------------------------------------------
    // APPLY FILTERS
    // ------------------------------------------------------------

    // Status filter
    if ($status) {
        $sql .= " AND p.status = :status";
        $params[':status'] = $status;
    }

    // Category filter
    if ($category) {
        $sql .= "
            AND p.id IN (
                SELECT pc.project_id
                FROM project_categories pc
                INNER JOIN categories c ON c.id = pc.category_id
                WHERE c.name = :category
            )
        ";
        $params[':category'] = $category;
    }

    // Tag filter
    if ($tag) {
        $sql .= "
            AND p.id IN (
                SELECT pt.project_id
                FROM project_tags pt
                INNER JOIN tags t ON t.id = pt.tag_id
                WHERE t.name = :tag
            )
        ";
        $params[':tag'] = $tag;
    }

    // Creator filter
    if ($creator) {
        $sql .= "
            AND p.id IN (
                SELECT pc.project_id
                FROM project_creators pc
                INNER JOIN creators c ON c.id = pc.creator_id
                WHERE c.name = :creator
            )
        ";
        $params[':creator'] = $creator;
    }

    // ------------------------------------------------------------
    // ORDERING
    // ------------------------------------------------------------
    $sql .= " ORDER BY p.created_at DESC";

    // ------------------------------------------------------------
    // EXECUTE QUERY
    // ------------------------------------------------------------
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($projects);
}

function create_project() {
    $db = get_db();

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        return;
    }

    // Required fields
    $title = $data['title'] ?? null;
    $description = $data['description'] ?? null;

    if (!$title || !$description) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    // Optional relational fields
    $creators   = $data['creators']   ?? [];
    $categories = $data['categories'] ?? [];
    $tags       = $data['tags']       ?? [];
    $images     = $data['images']     ?? [];

    // New status field
    $status = $data['status'] ?? 'in_progress';

    $baseSlug = generate_slug($title); 
    $slug = ensure_unique_slug($db, $baseSlug);

    // ------------------------------------------------------------
    // INSERT MAIN PROJECT
    // ------------------------------------------------------------
    $stmt = $db->prepare("
        INSERT INTO projects (title, slug, description, status, created_at)
        VALUES (:title, :slug, :description, :status, NOW())
    ");

    $stmt->execute([
        ':title'       => $title,
        ':description' => $description,
        ':status'      => $status,
        ':slug'        => $slug
    ]);

    $project_id = $db->lastInsertId();

    // ------------------------------------------------------------
    // CREATORS (IDs only)
    // ------------------------------------------------------------
    if (!empty($creators)) {
        $stmt = $db->prepare("
            INSERT INTO project_creators (project_id, creator_id)
            VALUES (:pid, :cid)
        ");

        foreach ($creators as $creator_id) {
            $stmt->execute([
                ':pid' => $project_id,
                ':cid' => $creator_id
            ]);
        }
    }

    // ------------------------------------------------------------
    // CATEGORY HANDLING (names → ensure exists → pivot)
    // ------------------------------------------------------------
    if (!empty($categories) && is_array($categories)) {
        foreach ($categories as $catName) {

            // 1. Check if category exists
            $stmt = $db->prepare("SELECT id FROM categories WHERE name = ?");
            $stmt->execute([$catName]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Create if missing
            if (!$category) {
                $stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
                $stmt->execute([$catName]);
                $categoryId = $db->lastInsertId();
            } else {
                $categoryId = $category['id'];
            }

            // 3. Attach to project
            $stmt = $db->prepare("
                INSERT INTO project_categories (project_id, category_id)
                VALUES (?, ?)
            ");
            $stmt->execute([$project_id, $categoryId]);
        }
    }

    // ------------------------------------------------------------
    // TAG HANDLING (names → ensure exists → pivot)
    // ------------------------------------------------------------
    if (!empty($tags) && is_array($tags)) {
        foreach ($tags as $tagName) {

            // 1. Check if tag exists
            $stmt = $db->prepare("SELECT id FROM tags WHERE name = ?");
            $stmt->execute([$tagName]);
            $tag = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Create if missing
            if (!$tag) {
                $stmt = $db->prepare("INSERT INTO tags (name) VALUES (?)");
                $stmt->execute([$tagName]);
                $tagId = $db->lastInsertId();
            } else {
                $tagId = $tag['id'];
            }

            // 3. Attach to project
            $stmt = $db->prepare("
                INSERT INTO project_tags (project_id, tag_id)
                VALUES (?, ?)
            ");
            $stmt->execute([$project_id, $tagId]);
        }
    }

    // ------------------------------------------------------------
    // IMAGES
    // ------------------------------------------------------------
    if (!empty($images)) {
        $stmt = $db->prepare("
            INSERT INTO project_images (project_id, image_url)
            VALUES (:pid, :url)
        ");

        foreach ($images as $img) {
            if (!empty($img['image_url'])) {
                $stmt->execute([
                    ':pid' => $project_id,
                    ':url' => $img['image_url']
                ]);
            }
        }
    }

    echo json_encode([
        'success'    => true,
        'project_id' => $project_id
    ]);
}

function get_project_by_id($id) {
    $db = get_db();

    // ------------------------------------------------------------
    // FETCH MAIN PROJECT
    // ------------------------------------------------------------
    $stmt = $db->prepare("
        SELECT id, title, description, status, slug, created_at, updated_at
        FROM projects
        WHERE id = ?
    ");
    $stmt->execute([$id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        http_response_code(404);
        echo json_encode(['error' => 'Project not found']);
        return;
    }

    // ------------------------------------------------------------
    // FETCH CREATORS
    // ------------------------------------------------------------
    $stmt = $db->prepare("
        SELECT c.id, c.name
        FROM creators c
        INNER JOIN project_creators pc ON pc.creator_id = c.id
        WHERE pc.project_id = ?
    ");
    $stmt->execute([$id]);
    $project['creators'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ------------------------------------------------------------
    // FETCH CATEGORIES
    // ------------------------------------------------------------
    $stmt = $db->prepare("
        SELECT cat.id, cat.name
        FROM categories cat
        INNER JOIN project_categories pc ON pc.category_id = cat.id
        WHERE pc.project_id = ?
    ");
    $stmt->execute([$id]);
    $project['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ------------------------------------------------------------
    // FETCH TAGS
    // ------------------------------------------------------------
    $stmt = $db->prepare("
        SELECT t.id, t.name
        FROM tags t
        INNER JOIN project_tags pt ON pt.tag_id = t.id
        WHERE pt.project_id = ?
    ");
    $stmt->execute([$id]);
    $project['tags'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ------------------------------------------------------------
    // FETCH IMAGES
    // ------------------------------------------------------------
    $stmt = $db->prepare("
        SELECT id, image_url
        FROM project_images
        WHERE project_id = ?
    ");
    $stmt->execute([$id]);
    $project['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ------------------------------------------------------------
    // RETURN FULLY HYDRATED PROJECT
    // ------------------------------------------------------------
    echo json_encode($project);
}

function update_project($id) {
    $db = get_db();

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        return;
    }

    // Required fields
    $title = $data['title'] ?? null;
    $description = $data['description'] ?? null;
    $baseSlug = generate_slug($title);
    $slug = ensure_unique_slug($db, $baseSlug);


    if (!$title || !$description) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    // Optional relational fields
    $creators   = $data['creators']   ?? [];
    $categories = $data['categories'] ?? [];
    $tags       = $data['tags']       ?? [];
    $images     = $data['images']     ?? [];

    // Status field
    $status = $data['status'] ?? 'in_progress';

    // ------------------------------------------------------------
    // UPDATE MAIN PROJECT
    // ------------------------------------------------------------
    $stmt = $db->prepare("
        UPDATE projects
        SET title = :title,
            slug = :slug,
            description = :description,
            status = :status,
            updated_at = NOW()
        WHERE id = :id

    ");

    $stmt->execute([
        ':title'       => $title,
        ':description' => $description,
        ':status'      => $status,
        ':id'          => $id
    ]);

    // ------------------------------------------------------------
    // CLEAR OLD RELATIONAL DATA
    // ------------------------------------------------------------
    $db->prepare("DELETE FROM project_creators WHERE project_id = ?")->execute([$id]);
    $db->prepare("DELETE FROM project_categories WHERE project_id = ?")->execute([$id]);
    $db->prepare("DELETE FROM project_tags WHERE project_id = ?")->execute([$id]);
    $db->prepare("DELETE FROM project_images WHERE project_id = ?")->execute([$id]);

    // ------------------------------------------------------------
    // CREATORS (IDs only)
    // ------------------------------------------------------------
    if (!empty($creators)) {
        $stmt = $db->prepare("
            INSERT INTO project_creators (project_id, creator_id)
            VALUES (:pid, :cid)
        ");

        foreach ($creators as $creator_id) {
            $stmt->execute([
                ':pid' => $id,
                ':cid' => $creator_id
            ]);
        }
    }

    // ------------------------------------------------------------
    // CATEGORY HANDLING (names → ensure exists → pivot)
    // ------------------------------------------------------------
    if (!empty($categories) && is_array($categories)) {
        foreach ($categories as $catName) {

            // 1. Check if category exists
            $stmt = $db->prepare("SELECT id FROM categories WHERE name = ?");
            $stmt->execute([$catName]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Create if missing
            if (!$category) {
                $stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
                $stmt->execute([$catName]);
                $categoryId = $db->lastInsertId();
            } else {
                $categoryId = $category['id'];
            }

            // 3. Attach to project
            $stmt = $db->prepare("
                INSERT INTO project_categories (project_id, category_id)
                VALUES (?, ?)
            ");
            $stmt->execute([$id, $categoryId]);
        }
    }

    // ------------------------------------------------------------
    // TAG HANDLING (names → ensure exists → pivot)
    // ------------------------------------------------------------
    if (!empty($tags) && is_array($tags)) {
        foreach ($tags as $tagName) {

            // 1. Check if tag exists
            $stmt = $db->prepare("SELECT id FROM tags WHERE name = ?");
            $stmt->execute([$tagName]);
            $tag = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Create if missing
            if (!$tag) {
                $stmt = $db->prepare("INSERT INTO tags (name) VALUES (?)");
                $stmt->execute([$tagName]);
                $tagId = $db->lastInsertId();
            } else {
                $tagId = $tag['id'];
            }

            // 3. Attach to project
            $stmt = $db->prepare("
                INSERT INTO project_tags (project_id, tag_id)
                VALUES (?, ?)
            ");
            $stmt->execute([$id, $tagId]);
        }
    }

    // ------------------------------------------------------------
    // IMAGES
    // ------------------------------------------------------------
    if (!empty($images)) {
        $stmt = $db->prepare("
            INSERT INTO project_images (project_id, image_url)
            VALUES (:pid, :url)
        ");

        foreach ($images as $img) {
            if (!empty($img['image_url'])) {
                $stmt->execute([
                    ':pid' => $id,
                    ':url' => $img['image_url']
                ]);
            }
        }
    }

    echo json_encode([
        'success'    => true,
        'updated_id' => $id
    ]);
}

function delete_project($id) {
    global $conn;

    // Check if project exists
    $stmt = $conn->prepare("SELECT id FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        send_response(["error" => "Project not found"], 404);
        return;
    }

    // --- RELATIONAL CLEANUP ---

    // Delete creators pivot
    $stmt = $conn->prepare("DELETE FROM project_creators WHERE project_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Delete categories pivot
    $stmt = $conn->prepare("DELETE FROM project_categories WHERE project_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Delete tags pivot
    $stmt = $conn->prepare("DELETE FROM project_tags WHERE project_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Delete images
    $stmt = $conn->prepare("DELETE FROM project_images WHERE project_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Delete the project itself
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    send_response([
        "success" => true,
        "deleted_id" => $id
    ]);
}
