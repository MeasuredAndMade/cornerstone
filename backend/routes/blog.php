<?php
require_once __DIR__ . '/../db.php';

function list_blog_posts() {
    $db = get_db();

    $stmt = $db->query('SELECT * FROM blog_posts ORDER BY id DESC');
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($posts);
}