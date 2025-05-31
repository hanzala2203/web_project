-- Clean up existing data
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM student_interests;
DELETE FROM module_prerequisites;
DELETE FROM programme_modules;
DELETE FROM assignments;
DELETE FROM student_enrollments;
DELETE FROM modules;
DELETE FROM programmes;
DELETE FROM departments;

SET FOREIGN_KEY_CHECKS = 1;

-- Reset auto-increment values
ALTER TABLE modules AUTO_INCREMENT = 1;
ALTER TABLE programmes AUTO_INCREMENT = 1;
