-- Create student_programmes table if not exists
CREATE TABLE IF NOT EXISTS student_programmes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    programme_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'active',
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE,
    UNIQUE KEY student_programme_pair (student_id, programme_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert some sample data if not exists
INSERT INTO student_programmes (student_id, programme_id)
SELECT u.id, p.id
FROM users u
CROSS JOIN programmes p
WHERE u.role = 'student'
AND NOT EXISTS (
    SELECT 1 FROM student_programmes sp
    WHERE sp.student_id = u.id AND sp.programme_id = p.id
)
LIMIT 1;
