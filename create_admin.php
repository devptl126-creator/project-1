<?php
require_once 'config.php';

$email     = 'tanay@gmail.com';
$password  = 'tanay';
$role      = 'admin';
$full_name = 'System Administrator';
$phone     = '+91 9999999999';

// Hash the password
$hashed = password_hash($password, PASSWORD_BCRYPT);

// Check if email already exists in the system
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "Error: Email already registered.";
} else {
    $conn->begin_transaction();
    try {
        // Insert user record
        $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashed, $role);
        $stmt->execute();
        $user_id = $conn->insert_id;

        // Insert profile record
        $stmt = $conn->prepare("INSERT INTO profiles (user_id, full_name, phone) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $full_name, $phone);
        $stmt->execute();

        $conn->commit();
        echo "Administrator account created successfully! You can now log in at <a href='/roadside_ally_php/admin_login.php'>Admin Portal</a>.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Registration failed: " . $e->getMessage();
    }
}
?>