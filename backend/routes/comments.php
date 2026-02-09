<?php
require_once __DIR__ . '/../db.php';

function list_comments() {
    $db = get_db();

    $stmt = $db->query('SELECT * FROM comments ORDER BY id DESC');
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($comments);
}