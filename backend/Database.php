<?php
// Database configuration
$host = 'localhost';
$dbname = 'task_management'; // Replace with your actual database name
$username = 'root';     // Default XAMPP MySQL username
$password = '';         // Default XAMPP MySQL password (empty)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    return $pdo; // Return the PDO instance
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}