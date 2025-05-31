-- Add departments with diverse fields
INSERT INTO departments (name, description) VALUES
('Computer Science', 'Study of computation, algorithms, and software systems'),
('Business School', 'Management, finance, and entrepreneurship studies'),
('Engineering', 'Various engineering disciplines and applications'),
('Life Sciences', 'Biology, biochemistry, and biomedical sciences'),
('Arts & Humanities', 'Literature, history, and cultural studies');

-- Add diverse programmes across different fields
INSERT INTO programmes (title, description, level, duration, department, is_published) VALUES
('BSc Computer Science', 'Comprehensive study of programming, algorithms, and computer systems', 'undergraduate', '3 years', 'Computer Science', 1),
('MSc Artificial Intelligence', 'Advanced AI, machine learning, and data science', 'postgraduate', '1 year', 'Computer Science', 1),
('BSc Software Engineering', 'Software development lifecycle and engineering practices', 'undergraduate', '3 years', 'Computer Science', 1),
('BSc Business Administration', 'Business management and organizational studies', 'undergraduate', '3 years', 'Business School', 1),
('MSc Finance', 'Advanced financial analysis and investment strategies', 'postgraduate', '1 year', 'Business School', 1),
('BEng Mechanical Engineering', 'Design and analysis of mechanical systems', 'undergraduate', '4 years', 'Engineering', 1),
('MSc Biomedical Engineering', 'Engineering principles in medical applications', 'postgraduate', '2 years', 'Engineering', 1),
('BSc Biology', 'Study of living organisms and life processes', 'undergraduate', '3 years', 'Life Sciences', 1),
('BA English Literature', 'Analysis of literature and critical theory', 'undergraduate', '3 years', 'Arts & Humanities', 1),
('MA History', 'Advanced historical research and methodology', 'postgraduate', '1 year', 'Arts & Humanities', 1);

-- Add diverse modules across different years and semesters
INSERT INTO modules (title, description, credits, year_of_study, semester) VALUES
-- Computer Science modules
('Programming Fundamentals', 'Introduction to programming concepts using Python', 15, 1, 1),
('Data Structures & Algorithms', 'Essential data structures and algorithm analysis', 15, 1, 2),
('Database Systems', 'Database design and SQL programming', 15, 2, 1),
('Web Development', 'Full-stack web application development', 15, 2, 2),
('Artificial Intelligence', 'AI principles and applications', 15, 3, 1),
('Machine Learning', 'Statistical learning and pattern recognition', 15, 3, 2),
('Cybersecurity', 'Network security and cryptography', 15, 2, 1),

-- Business modules
('Business Economics', 'Economic principles for business', 15, 1, 1),
('Marketing Management', 'Marketing strategies and consumer behavior', 15, 1, 2),
('Financial Accounting', 'Principles of accounting and financial reporting', 15, 2, 1),

-- Engineering modules
('Engineering Mathematics', 'Mathematical methods for engineers', 15, 1, 1),
('Thermodynamics', 'Heat transfer and energy systems', 15, 2, 1),
('Robotics', 'Robot kinematics and control systems', 15, 3, 1),

-- Life Sciences modules
('Cell Biology', 'Structure and function of cells', 15, 1, 1),
('Genetics', 'Principles of inheritance and DNA', 15, 2, 1),

-- Arts & Humanities modules
('Literary Theory', 'Critical approaches to literature', 15, 1, 1),
('World History', 'Global historical developments', 15, 1, 2);

-- Link modules to programmes
INSERT INTO programme_modules (programme_id, module_id)
SELECT 
    p.id as programme_id,
    m.id as module_id
FROM programmes p, modules m
WHERE 
    (p.title = 'BSc Computer Science' AND m.title IN 
        ('Programming Fundamentals', 'Data Structures & Algorithms', 'Database Systems', 
         'Web Development', 'Artificial Intelligence', 'Machine Learning', 'Cybersecurity'))
    OR 
    (p.title = 'BSc Business Administration' AND m.title IN
        ('Business Economics', 'Marketing Management', 'Financial Accounting'))
    OR
    (p.title = 'BEng Mechanical Engineering' AND m.title IN
        ('Engineering Mathematics', 'Thermodynamics', 'Robotics'));

-- Add module prerequisites
INSERT INTO module_prerequisites (module_id, prerequisite_module_id)
SELECT m1.id, m2.id
FROM modules m1, modules m2
WHERE 
    (m1.title = 'Data Structures & Algorithms' AND m2.title = 'Programming Fundamentals')
    OR
    (m1.title = 'Web Development' AND m2.title = 'Programming Fundamentals')
    OR
    (m1.title = 'Artificial Intelligence' AND m2.title = 'Programming Fundamentals')
    OR
    (m1.title = 'Machine Learning' AND m2.title = 'Artificial Intelligence');
