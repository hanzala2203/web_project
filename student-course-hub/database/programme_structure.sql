-- Add level and image fields to programmes table
ALTER TABLE programmes
ADD COLUMN level ENUM('undergraduate', 'postgraduate') NOT NULL DEFAULT 'undergraduate',
ADD COLUMN image_url VARCHAR(255) DEFAULT NULL;

-- Create programme years table
CREATE TABLE IF NOT EXISTS programme_years (
    id INT PRIMARY KEY AUTO_INCREMENT,
    programme_id INT NOT NULL,
    year_number INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (programme_id) REFERENCES programmes(id)
);

-- Create programme semesters table
CREATE TABLE IF NOT EXISTS programme_semesters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    year_id INT NOT NULL,
    semester_number INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (year_id) REFERENCES programme_years(id)
);

-- Add image field to modules table
ALTER TABLE modules
ADD COLUMN image_url VARCHAR(255) DEFAULT NULL;

-- Create module staff table
CREATE TABLE IF NOT EXISTS module_staff (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    staff_id INT NOT NULL,
    role VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES modules(id),
    FOREIGN KEY (staff_id) REFERENCES users(id)
);

-- Add indexes for search performance
CREATE INDEX idx_programme_search ON programmes(title, level);
CREATE INDEX idx_module_search ON modules(title);

-- Insert sample undergraduate programme
INSERT INTO programmes (title, description, level, duration_years) VALUES 
('BSc Computer Science', 'A comprehensive degree covering software development, algorithms, and computer systems', 'undergraduate', 3);

-- Insert sample postgraduate programme
INSERT INTO programmes (title, description, level, duration_years) VALUES 
('MSc Cyber Security', 'Advanced study of security principles, cryptography, and network defense', 'postgraduate', 1);
