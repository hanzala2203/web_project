-- Drop existing table if it exists
DROP TABLE IF EXISTS student_interests;

-- Create new student_interests table
CREATE TABLE student_interests (
    interest_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    programme_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_programme (student_id, programme_id)
);
