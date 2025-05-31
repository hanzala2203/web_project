-- Create departments table if not exists
CREATE TABLE IF NOT EXISTS departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create module_staff table if not exists
CREATE TABLE IF NOT EXISTS module_staff (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    staff_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add department column to programmes table
ALTER TABLE programmes ADD COLUMN IF NOT EXISTS department VARCHAR(100);

-- Create programme_modules table if not exists (in case it's missing)
CREATE TABLE IF NOT EXISTS programme_modules (
    programme_id INT NOT NULL,
    module_id INT NOT NULL,
    PRIMARY KEY (programme_id, module_id),
    FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
);

-- Create module_prerequisites table if not exists
CREATE TABLE IF NOT EXISTS module_prerequisites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    prerequisite_module_id INT NOT NULL,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    FOREIGN KEY (prerequisite_module_id) REFERENCES modules(id) ON DELETE CASCADE,
    UNIQUE KEY module_prerequisite_pair (module_id, prerequisite_module_id)
);

-- Create student_interests table if not exists
CREATE TABLE IF NOT EXISTS student_interests (
    student_id INT NOT NULL,
    programme_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (student_id, programme_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE
);
