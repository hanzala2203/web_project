<?php if (!defined('BASE_URL')) define('BASE_URL', '/student-course-hub'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>Student Course Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/css/student.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="wrapper">
    <?php require_once __DIR__ . '/student_sidebar.php'; ?>
