<?php
// $staff, $data, and $errors are assumed to be passed from the controller.
// $staff is the existing staff data for the form.
// $data is for repopulating form on validation error (if different from $staff).
// $errors is for displaying validation errors.

$pageTitle = "Edit Staff Member";
// Use a descriptive name for the header, like username or full_name if available
$staffName = htmlspecialchars($staff['username'] ?? ($staff['full_name'] ?? 'N/A'));
$headerText = "Edit Staff: " . $staffName;
$breadcrumbText = "Edit";
$activeMenu = "staff"; // For highlighting the active menu item in the sidebar

// Fallback for $data if not set (e.g., first load of edit page)
if (!isset($data) || empty($data)) {
    $data = $staff ?? [];
}
$errors = $errors ?? [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style type="text/css">
        /* Basic layout and typography */
        body { font-family: sans-serif; margin: 0; background-color: #f4f7f6; color: #333; box-sizing: border-box; }
        *, *:before, *:after { box-sizing: inherit; } /* Ensure all elements use border-box */
        .admin-container { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 250px; background: #2c3e50; color: #ecf0f1; padding: 20px; position: fixed; height: 100%; top: 0; left: 0; overflow-y: auto; z-index: 1000;}
        .admin-main { margin-left: 250px; padding: 20px; flex-grow: 1; background-color: #fff; width: calc(100% - 250px); /* Ensure it takes remaining width */ }
        .admin-header-main { margin-bottom: 20px; padding-bottom:10px; border-bottom: 1px solid #ddd; }
        .admin-header-main h1 { margin: 0; font-size: 1.8em; }
        
        /* Form specific styles */
        .form-group { margin-bottom: 1rem; }
        .form-control { display: block; width: 100%; padding: .5rem .75rem; font-size: .9rem; line-height: 1.5; color: #495057; background-color: #fff; background-clip: padding-box; border: 1px solid #ced4da; border-radius: .25rem; transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out; box-sizing: border-box;}
        .form-control:focus { border-color: #80bdff; outline: 0; box-shadow: 0 0 0 .2rem rgba(0,123,255,.25); }
        label { display: inline-block; margin-bottom: .5rem; font-weight: 500; }
        .required { color: red; }
        .invalid-feedback { display: none; width: 100%; margin-top: .25rem; font-size: 80%; color: #dc3545; }
        .form-control.is-invalid { border-color: #dc3545; }
        .form-control.is-invalid ~ .invalid-feedback { display: block; }
        textarea.form-control { height: auto; }

        /* Button styles */
        .btn { display: inline-block; font-weight: 400; color: #212529; text-align: center; vertical-align: middle; cursor: pointer; user-select: none; background-color: transparent; border: 1px solid transparent; padding: .375rem .75rem; font-size: 1rem; line-height: 1.5; border-radius: .25rem; transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out; }
        .btn-primary { color: #fff; background-color: #007bff; border-color: #007bff; }
        .btn-primary:hover { color: #fff; background-color: #0056b3; border-color: #0056b3; }
        .btn-secondary { color: #fff; background-color: #6c757d; border-color: #6c757d; }
        .btn-secondary:hover { color: #fff; background-color: #545b62; border-color: #545b62; }
        .form-actions { margin-top: 1.5rem; }
        .form-actions .btn { margin-right: 0.5rem; }
        .form-actions .btn i { margin-right: 0.3rem; }

        /* Alert styles */
        .alert { position: relative; padding: .75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert ul { margin-bottom: 0; padding-left: 20px;}

        /* Sidebar specific (minimal for create/edit pages) */
        .admin-sidebar nav ul { list-style: none; padding: 0; margin: 0; }
        .admin-sidebar nav ul li a { display: block; padding: 10px 15px; color: #ecf0f1; text-decoration: none; border-left: 3px solid transparent; }
        .admin-sidebar nav ul li a:hover, .admin-sidebar nav ul li.active a { background-color: #34495e; border-left-color: #3498db; }
        .admin-sidebar nav ul li a i { margin-right: 8px; }
        .sidebar-header .logo { font-size: 1.5em; color: #fff; text-decoration: none; display: block; margin-bottom: 1rem; text-align: center;}

        /* Breadcrumb */
        .breadcrumb { display: flex; flex-wrap: wrap; padding: .75rem 1rem; margin-bottom: 1rem; list-style: none; background-color: #e9ecef; border-radius: .25rem; }
        .breadcrumb-item + .breadcrumb-item::before { display: inline-block; padding-right: .5rem; padding-left: .5rem; color: #6c757d; content: "/"; }
        .breadcrumb-item.active { color: #6c757d; }
        .breadcrumb-item a { color: #007bff; text-decoration: none; background-color: transparent; }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include_once __DIR__ . '/../../layouts/sidebar.php'; ?>

        <div class="admin-main">
            <header class="admin-header-main">
                 <div class="header-left">
                    <h1><?php echo $headerText; // Already HTML escaped ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/student-course-hub/admin/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/student-course-hub/admin/staff">Staff</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($breadcrumbText); ?></li>
                        </ol>
                    </nav>
                </div>
            </header>

            <main class="content-area">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <strong>Whoops! Something went wrong.</strong>
                        <ul class="mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (empty($staff) || !isset($staff['id'])): ?>
                    <div class="alert alert-warning">Staff member not found or could not be loaded.</div>
                <?php else: ?>
                    <form method="POST" action="/student-course-hub/admin/staff/<?php echo htmlspecialchars($staff['id']); ?>/update">
                        <input type="hidden" name="_method" value="PUT"> <!-- Or PATCH depending on your route -->

                        <div class="form-group">
                            <label for="username">Username <span class="required">*</span></label>
                            <input type="text" name="username" id="username" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data['username'] ?? ''); ?>" required>
                            <?php if (isset($errors['username'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['username']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="full_name">Full Name</label> <!-- Optional, if your system uses it -->
                            <input type="text" name="full_name" id="full_name" class="form-control <?php echo isset($errors['full_name']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data['full_name'] ?? ''); ?>">
                            <?php if (isset($errors['full_name'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['full_name']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address <span class="required">*</span></label>
                            <input type="email" name="email" id="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <hr>
                        <p class="text-muted">Leave password fields blank to keep the current password.</p>

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" name="password" id="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>">
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['password']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control <?php echo isset($errors['password_confirmation']) ? 'is-invalid' : ''; ?>">
                            <?php if (isset($errors['password_confirmation'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['password_confirmation']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Staff Member</button>
                            <a href="/student-course-hub/admin/staff" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                        </div>
                    </form>
                <?php endif; ?>
            </main>
            <?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
        </div>
    </div>
</body>
</html>
