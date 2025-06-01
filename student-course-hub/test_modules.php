<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_course_hub');
define('DB_USER', 'root');
define('DB_PASS', '');

echo "Starting module insertion script...\n";

try {
    // Create PDO connection
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, 
        DB_USER, 
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!\n\n";

    // Sample module data
    $modules = [
        [
            'title' => 'Introduction to Programming',
            'description' => 'Fundamental concepts of programming using Python. Topics include variables, data types, control structures, functions, and basic object-oriented programming.',
            'credits' => 15,
            'year_of_study' => 1,
            'semester' => 1,
            'programme_id' => 1,  // Assuming Computer Science programme has ID 1
            'staff_id' => 1       // Assuming we have a staff member with ID 1
        ],
        [
            'title' => 'Web Development Basics',
            'description' => 'Introduction to HTML, CSS and JavaScript. Learn to create responsive web pages and understand web development principles.',
            'credits' => 15,
            'year_of_study' => 1,
            'semester' => 2,
            'programme_id' => 1,
            'staff_id' => 1
        ],
        [
            'title' => 'Data Structures and Algorithms',
            'description' => 'Study of fundamental data structures and algorithms. Topics include arrays, linked lists, trees, sorting algorithms, and complexity analysis.',
            'credits' => 20,
            'year_of_study' => 2,
            'semester' => 1,
            'programme_id' => 1,
            'staff_id' => 2
        ],
        [
            'title' => 'Database Systems',
            'description' => 'Introduction to database design and SQL. Covers ER diagrams, normalization, and database management systems.',
            'credits' => 20,
            'year_of_study' => 2,
            'semester' => 2,
            'programme_id' => 1,
            'staff_id' => 2
        ],
        [
            'title' => 'Software Engineering',
            'description' => 'Software development lifecycle and best practices. Learn about requirements gathering, system design, testing, and project management.',
            'credits' => 30,
            'year_of_study' => 3,
            'semester' => 1,
            'programme_id' => 1,
            'staff_id' => 1
        ]
    ];    // First, let's check if we have valid programme_id and staff_id
    $checkProgramme = $pdo->query("SELECT id FROM programmes WHERE title = 'Computer Science' LIMIT 1");
    $programme = $checkProgramme->fetch(PDO::FETCH_ASSOC);
    
    if (!$programme) {
        echo "Creating sample programme...\n";
        $pdo->exec("INSERT INTO programmes (title, description, level, is_published) VALUES 
                   ('Computer Science', 'Bachelor of Science in Computer Science', 'undergraduate', 1)");
        $programmeId = $pdo->lastInsertId();
    } else {
        $programmeId = $programme['id'];
    }

    // Update programme_id in modules
    foreach ($modules as &$module) {
        $module['programme_id'] = $programmeId;
    }
    unset($module);

    // Check for staff members
    $checkStaff = $pdo->query("SELECT id FROM users WHERE role = 'staff' ORDER BY id ASC LIMIT 2");
    $staff = $checkStaff->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($staff) < 2) {
        echo "Creating sample staff members...\n";
        // Delete existing staff if any
        $pdo->exec("DELETE FROM users WHERE role = 'staff'");
        
        // Create new staff
        $pdo->exec("INSERT INTO users (username, email, password, role) VALUES 
                   ('john.doe', 'john.doe@faculty.edu', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'staff'),
                   ('jane.smith', 'jane.smith@faculty.edu', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'staff')");
        
        // Get the new staff IDs
        $staff = $pdo->query("SELECT id FROM users WHERE role = 'staff' ORDER BY id ASC LIMIT 2")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update staff_id in modules
    $staffId1 = $staff[0]['id'];
    $staffId2 = $staff[1]['id'] ?? $staff[0]['id'];
    
    foreach ($modules as &$module) {
        $module['staff_id'] = ($module['staff_id'] == 1) ? $staffId1 : $staffId2;
    }
    unset($module);

    // Insert modules
    $stmt = $pdo->prepare("
        INSERT INTO modules 
        (title, description, credits, year_of_study, semester, programme_id, staff_id, created_at) 
        VALUES 
        (:title, :description, :credits, :year_of_study, :semester, :programme_id, :staff_id, NOW())
    ");

    foreach ($modules as $module) {
        echo "Inserting module: {$module['title']}\n";
        $stmt->execute($module);
    }

    echo "\nAll modules inserted successfully!\n";

    // Verify insertion
    $result = $pdo->query("SELECT id, title, credits, year_of_study, semester FROM modules ORDER BY year_of_study, semester");
    echo "\nVerifying inserted modules:\n";
    echo str_repeat('-', 80) . "\n";
    echo sprintf("%-5s %-40s %-8s %-5s %-8s\n", "ID", "Title", "Credits", "Year", "Semester");
    echo str_repeat('-', 80) . "\n";
    
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-5s %-40s %-8s %-5s %-8s\n",
            $row['id'],
            substr($row['title'], 0, 39),
            $row['credits'],
            $row['year_of_study'],
            $row['semester']
        );
    }
    echo str_repeat('-', 80) . "\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
