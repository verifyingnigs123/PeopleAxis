<?php
$host = '127.0.0.1';
$db = 'peopleaxis';
$user = 'root';
$pass = '';
$port = 3306;
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
$pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$stmt = $pdo->query('SELECT id, name, privileges, deleted_at FROM roles ORDER BY id');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT);
