<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Course Hub</title>
    <style>
    <?php 
        $stylePath = BASE_PATH . '/public/assets/css/style.css';
        if (file_exists($stylePath)) {
            echo file_get_contents($stylePath);
        }
        
        $authStylePath = BASE_PATH . '/public/assets/css/auth.css';
        if (file_exists($authStylePath)) {
            echo file_get_contents($authStylePath);
        }
    ?>
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../../public/assets/css/admin-dashboard.css">
</head>
<body>
    <header>
        <h1>Welcome to the Student Course Hub</h1>
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li><a href="<?php echo BASE_URL; ?>/auth/login">Login</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/auth/register">Register</a></li>
                <?php else: ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="<?php echo BASE_URL; ?>/admin/dashboard">Admin Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>/student/dashboard">Student Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo BASE_URL; ?>/auth/logout">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>