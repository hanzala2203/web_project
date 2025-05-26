-- Add semester column to modules table if not exists
ALTER TABLE modules ADD COLUMN IF NOT EXISTS semester INT DEFAULT 1 AFTER year_of_study;

-- Create module prerequisites table if not exists
CREATE TABLE IF NOT EXISTS module_prerequisites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    prerequisite_module_id INT NOT NULL,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    FOREIGN KEY (prerequisite_module_id) REFERENCES modules(id) ON DELETE CASCADE,
    UNIQUE KEY module_prerequisite_pair (module_id, prerequisite_module_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create student notifications table if not exists
CREATE TABLE IF NOT EXISTS student_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) DEFAULT "info",
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create student programme enrollments table if not exists
CREATE TABLE IF NOT EXISTS student_programmes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    programme_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT "active",
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE,
    UNIQUE KEY student_programme_pair (student_id, programme_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
