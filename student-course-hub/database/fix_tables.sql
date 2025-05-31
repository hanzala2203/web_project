-- Add avatar_url to users table if not exists
ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar_url VARCHAR(255) DEFAULT NULL;

-- Add created_at to student_interests table if not exists
ALTER TABLE student_interests ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Create programme_features table if not exists
CREATE TABLE IF NOT EXISTS programme_features (
    id INT PRIMARY KEY AUTO_INCREMENT,
    programme_id INT NOT NULL,
    feature_name VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE
);

-- Ensure is_published column exists in programmes
ALTER TABLE programmes ADD COLUMN IF NOT EXISTS is_published BOOLEAN DEFAULT TRUE;

-- Ensure level column exists and is ENUM
ALTER TABLE programmes MODIFY COLUMN level ENUM('undergraduate', 'postgraduate') NOT NULL DEFAULT 'undergraduate';
