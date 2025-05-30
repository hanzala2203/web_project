CREATE TABLE IF NOT EXISTS module_prerequisites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    prerequisite_module_id INT NOT NULL,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    FOREIGN KEY (prerequisite_module_id) REFERENCES modules(id) ON DELETE CASCADE,
    UNIQUE KEY module_prerequisite_pair (module_id, prerequisite_module_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
