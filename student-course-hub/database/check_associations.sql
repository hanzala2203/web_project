-- Check for modules without programme associations
SELECT m.id, m.title, GROUP_CONCAT(p.title) as programmes
FROM modules m
LEFT JOIN programme_modules pm ON m.id = pm.module_id
LEFT JOIN programmes p ON pm.programme_id = p.id
GROUP BY m.id, m.title
ORDER BY m.id;
