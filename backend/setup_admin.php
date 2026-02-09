<?php
require_once __DIR__ . '/db.php';

$db = get_db();

$username = 'admin';
$password = 'Angel89!';
$name = 'Kimerlee Carroll';

$hash = password_hash($password, PASSWORD_DEFAULT);

// 1. Create the admin user
$stmt = $db->prepare("
    INSERT INTO users (username, password, role)
    VALUES (:username, :password, 'admin')
");
$stmt->execute([
    ':username' => $username,
    ':password' => $hash
]);

$user_id = $db->lastInsertId();

// 2. Automatically create a matching creator
$stmt = $db->prepare("
    INSERT INTO creators (name, user_id)
    VALUES (:name, :user_id)
");
$stmt->execute([
    ':name' => $name,
    ':user_id' => $user_id
]);

echo "Admin + Creator created successfully.";
