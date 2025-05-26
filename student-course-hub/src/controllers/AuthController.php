<?php

namespace App\Controllers;

use App\Models\User;
use Exception;

class AuthController {
    private $user;
    private $maxLoginAttempts = 5;
    private $lockoutTime = 900; // 15 minutes

    public function __construct() {
        $this->user = new User();
    }

    private function checkLoginAttempts($email) {
        $attempts = isset($_SESSION['login_attempts'][$email]) 
            ? $_SESSION['login_attempts'][$email] : ['count' => 0, 'time' => time()];
        
        if ($attempts['count'] >= $this->maxLoginAttempts) {
            if (time() - $attempts['time'] < $this->lockoutTime) {
                throw new Exception("Account temporarily locked. Please try again later.");
            }
            // Reset attempts after lockout period
            $_SESSION['login_attempts'][$email] = ['count' => 0, 'time' => time()];
        }
    }

    private function updateLoginAttempts($email) {
        if (!isset($_SESSION['login_attempts'][$email])) {
            $_SESSION['login_attempts'][$email] = ['count' => 0, 'time' => time()];
        }
        $_SESSION['login_attempts'][$email]['count']++;
        $_SESSION['login_attempts'][$email]['time'] = time();
    }

    public function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCSRFToken($token) {
        if (empty($token) || empty($_SESSION['csrf_token']) || 
            !hash_equals($_SESSION['csrf_token'], $token)) {
            throw new Exception("Invalid CSRF token");
        }
    }

    public function login($email, $password) {
        try {
            $this->checkLoginAttempts($email);
            
            // Validate input
            if (empty($email) || empty($password)) {
                throw new Exception("Email and password are required");
            }

            // Sanitize email
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }

            // Debug log
            error_log("Attempting login for email: " . $email);

            // Attempt authentication
            $user = $this->user->findByEmail($email);
            
            // Debug log
            error_log("User found: " . ($user ? "yes" : "no"));
            if ($user) {
                error_log("User role: " . $user['role']);
                error_log("Password verification result: " . (password_verify($password, $user['password']) ? "true" : "false"));
            }

            if (!$user || !password_verify($password, $user['password'])) {
                $this->updateLoginAttempts($email);
                throw new Exception("Invalid credentials");
            }

            // Store user data in session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];

            // Debug log
            error_log("Session created with user_id: " . $_SESSION['user_id'] . ", role: " . $_SESSION['role']);

            // Set secure session cookie params
            $cookieParams = session_get_cookie_params();
            session_set_cookie_params([
                'lifetime' => 3600,
                'path' => '/',
                'domain' => $cookieParams['domain'],
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            // Clear login attempts on successful login
            unset($_SESSION['login_attempts'][$email]);
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: ' . BASE_URL . '/admin/dashboard');
            } else if ($user['role'] === 'staff') {
                header('Location: ' . BASE_URL . '/staff/dashboard');
            } else {
                header('Location: ' . BASE_URL . '/student/dashboard');
            }
            exit();
        } catch (Exception $e) {
            // Debug log
            error_log("Login error: " . $e->getMessage());
            throw $e;
        }
    }

    public function register($data) {
        try {
            // Validate required fields
            $requiredFields = ['username', 'email', 'password', 'confirm_password'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("$field is required");
                }
            }

            // Validate email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }

            // Validate password
            if (strlen($data['password']) < 8) {
                throw new Exception("Password must be at least 8 characters long");
            }

            if ($data['password'] !== $data['confirm_password']) {
                throw new Exception("Passwords do not match");
            }

            // Check if email already exists
            if ($this->user->emailExists($data['email'])) {
                throw new Exception("Email already registered");
            }

            // Hash password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Set default role as student
            $data['role'] = 'student';

            // Create user
            $userId = $this->user->create($data);
            if (!$userId) {
                throw new Exception("Registration failed");
            }

            // If registering as a student, create student record
            if ($data['role'] === 'student') {
                require_once __DIR__ . '/../models/Student.php';
                $student = new Student($this->db);
                
                $studentData = [
                    'user_id' => $userId,
                    'name' => $data['username'],
                    'student_id' => 'STU' . str_pad($userId, 5, '0', STR_PAD_LEFT)
                ];
                
                if (!$student->create($studentData)) {
                    throw new Exception("Failed to create student profile");
                }
            }

            return true;
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            throw new Exception("Registration failed: " . $e->getMessage());
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Clear all session variables
        $_SESSION = array();
        
        // Clear session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        return true;
    }

    public function isAuthenticated() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'role' => $_SESSION['role'],
            'email' => $_SESSION['email'],
            'username' => $_SESSION['username']
        ];
    }

    public function requireAuth() {
        if (!$this->isAuthenticated()) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
    }

    public function requireRole($role) {
        $this->requireAuth();
        if ($_SESSION['role'] !== $role) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
    }

    public function resetPassword($email) {
        try {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }

            $user = $this->user->findByEmail($email);
            if (!$user) {
                throw new Exception("Email not found");
            }

            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store reset token
            $this->user->storeResetToken($user['id'], $token, $expiry);

            // Send reset email
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/auth/reset-password.php?token=" . $token;
            $to = $email;
            $subject = "Password Reset Request";
            $message = "Click the following link to reset your password: " . $resetLink;
            $headers = "From: noreply@example.com";

            mail($to, $subject, $message, $headers);

            return true;
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            throw new Exception("Password reset failed: " . $e->getMessage());
        }
    }
}