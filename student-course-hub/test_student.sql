-- Create test student
INSERT INTO users (username, email, password, role) 
VALUES ('test_student', 'test.student@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student');

-- Get the student's ID
SET @student_id = LAST_INSERT_ID();

-- Add student's interest in some programmes
INSERT INTO student_interests (student_id, programme_id)
SELECT @student_id, id FROM programmes WHERE level = 'undergraduate' LIMIT 1;

-- Enroll student in a programme
INSERT INTO student_programmes (student_id, programme_id, status)
SELECT @student_id, id, 'active' FROM programmes WHERE level = 'undergraduate' LIMIT 1;

-- Enroll student in some modules
INSERT INTO enrollments (student_id, module_id, status)
SELECT @student_id, id, 'enrolled' 
FROM modules 
WHERE year_of_study = 1 
LIMIT 3;
