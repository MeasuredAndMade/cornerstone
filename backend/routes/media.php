<?php
require_once __DIR__ . '/../db.php';

function list_media_items() {
    $db = get_db();

    $stmt = $db->query('SELECT * FROM media ORDER BY id DESC');
    $media = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($media);
}