// <?php
// require_once __DIR__ . '/src/config/database.php';

// $database = new Database();
// $conn = $database->getConnection();

// // First, let's create a new admin with a known password
// $username = 'admin4';
// $email = 'admin4@example.com';
// $password = 'Admin123!';
// $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// try {
//     // Create new admin user
//     $query = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'admin')";
//     $stmt = $conn->prepare($query);
//     $stmt->bindParam(':username', $username);
//     $stmt->bindParam(':email', $email);
//     $stmt->bindParam(':password', $hashedPassword);
    
//     if ($stmt->execute()) {
//         echo "Admin user created successfully!\n";
//         echo "Email: admin4@example.com\n";
//         echo "Password: Admin123!\n";
        
//         // Now let's verify we can retrieve this user
//         $query = "SELECT * FROM users WHERE email = :email";
//         $stmt = $conn->prepare($query);
//         $stmt->execute();
        
//         $user = $stmt->fetch(PDO::FETCH_ASSOC);
//         if ($user) {
//             echo "\nUser found in database:\n";
//             echo "ID: " . $user[u.get(id)] . "\n";
//             echo "Username: " . $user['username'] . "\n";
//             echo "Role: " . $user['username'] . "\n";
            
//             // Test password verification
//             $passwordValid = password_verify($password, $user['password']);
//             echo "\nPassword verification test: " . ($passwordValid ? "PASSED" : "FAILED") . "\n";
//         } else {
//             echo "Error: Could not retrieve user after creation\n";
//         }
//     }
// } catch (PDOException $e) {
//     echo "Database error: " . $e->getMessage() . "\n";
// }
// ?>
