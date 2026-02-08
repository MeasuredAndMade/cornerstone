<?php
require_once __DIR__ . '/db.php';

$db = get_db();
// choose admin password
$password = 'Angel89';
$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $db->prepare('UPDATE users SET password_hash = ? WHERE username = ?');
$stmt->execute([$hash, 'admin']);

echo "Admin password is successfully set";