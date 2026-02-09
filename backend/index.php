<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173'); //React Dev Server
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
} 

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

require_once __DIR__ . '/routes/auth.php';
require_once __DIR__ . '/routes/projects.php';
require_once __DIR__ . '/routes/blog.php';
require_once __DIR__ . '/routes/comments.php';
require_once __DIR__ . '/routes/media.php';
require_once __DIR__ . '/routes/creators.php';

switch (true) {
    case $path === '/api/login' && $method === 'POST':
        handle_login();
        break;

    case $path === '/api/projects' && $method === 'GET':
        error_log("Path Debug: " . $path);
        list_projects();
        break;

    case $path === '/api/projects' && $method === 'POST':
        create_project();
        break;

    // GET /api/projects/slug/:slug
    case preg_match('#^/api/projects/slug/([a-z0-9\-]+)$#', $path, $matches) && $method === 'GET':
        get_project_by_slug($matches[1]);
        break;

    case preg_match('#^/api/projects/(\d+)$#', $path, $matches) && $method === 'GET':
        get_project_by_id($matches[1]);
        break;

    case preg_match('#^/api/projects/(\d+)$#', $path, $matches) && $method === 'PUT':
        update_project($matches[1]);
        break;

    case preg_match('#^/api/projects/(\d+)$#', $path, $matches) && $method === 'DELETE':
        delete_project($matches[1]);
        break;

    case $path === '/api/blog' && $method === 'GET':
        list_blog_posts();
        break;

    case $path === '/api/media' && $method === 'GET':
        list_media_items();
        break;

    case $path === '/api/comments' && $method === 'GET':
        list_comments();
        break;

    case $path === '/api/creators':
        if ($method === 'GET') {
            get_creators();
            break;
        }
        if ($method === 'POST') {
            create_creator();
            break;
        }
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;

    case $path === '/api/categories' && $method === 'GET':
        list_categories();
        break;

    case $path === '/api/categories' && $method === 'POST':
        create_category();
        break;

    case $path === '/api/tags' && $method === 'GET':
        list_tags();
        break;

    case $path === '/api/tags' && $method === 'POST':
        create_tag();
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
}
