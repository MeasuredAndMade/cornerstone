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

require_once __DIR__ . '/routes/auth.php';
require_once __DIR__ . '/routes/projects.php';

switch (true) {
    case $path === '/api/login' && $_SERVER['REQUEST_METHOD'] === 'POST':
        handle_login();
        break;
    case $path === '/api/projects' && $_SERVER['REQUEST_METHOD'] === 'GET':
        list_projects();
        break;
    case $path === '/api/projects' && $_SERVER['REQUEST_METHOD'] === 'POST':
        create_project();
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
}