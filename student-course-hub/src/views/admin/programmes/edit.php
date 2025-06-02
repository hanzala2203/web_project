<?php
// $programme, $data, and $errors are assumed to be passed from the controller.
// $programme is the existing programme data for the form.
// $data is for repopulating form on validation error (if different from $programme).
// $errors is for displaying validation errors.

$pageTitle = "Edit Programme";
$headerText = "Edit Programme: " . htmlspecialchars($programme['name'] ?? 'N/A');
$breadcrumbText = "Edit";
$activeMenu = "programmes"; // For highlighting the active menu item in the sidebar

// Fallback for $data if not set (e.g., first load of edit page)
if (!isset($data) || empty($data)) {
    $data = $programme ?? [];
}
$errors = $errors ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Admin</title>    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#3b82f6',
                            hover: '#2563eb'
                        },
                        secondary: {
                            DEFAULT: '#4CAF50',
                            hover: '#45a049'
                        },
                        error: '#ef4444',
                        background: '#f1f5f9',
                    }
                }
            }
        }
    </script>
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
                            <li class="breadcrumb-item"><a href="/student-course-hub/admin/programmes">Programmes</a></li>
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

                <?php if (empty($programme)): ?>
                    <div class="alert alert-warning">Programme not found or could not be loaded.</div>
                <?php else: ?>                    <form method="POST" action="/student-course-hub/admin/programmes/<?php echo htmlspecialchars($programme['id']); ?>/update">
                        <div class="form-group">
                            <label for="title">Programme Name <span class="required">*</span></label>
                            <input type="text" name="title" id="title" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data['title'] ?? $programme['title'] ?? ''); ?>" required>
                            <?php if (isset($errors['title'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['title']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" rows="4"><?php echo htmlspecialchars($data['description'] ?? $programme['description'] ?? ''); ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['description']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="level">Level <span class="required">*</span></label>
                            <select name="level" id="level" class="form-control <?php echo isset($errors['level']) ? 'is-invalid' : ''; ?>" required>
                                <option value="undergraduate" <?php echo (($data['level'] ?? $programme['level']) === 'undergraduate') ? 'selected' : ''; ?>>Undergraduate</option>
                                <option value="postgraduate" <?php echo (($data['level'] ?? $programme['level']) === 'postgraduate') ? 'selected' : ''; ?>>Postgraduate</option>
                                <option value="doctorate" <?php echo (($data['level'] ?? $programme['level']) === 'doctorate') ? 'selected' : ''; ?>>Doctorate</option>
                            </select>
                            <?php if (isset($errors['level'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['level']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="duration">Duration <span class="required">*</span></label>
                            <input type="text" name="duration" id="duration" placeholder="e.g. 3 years" class="form-control <?php echo isset($errors['duration']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data['duration'] ?? $programme['duration'] ?? ''); ?>" required>
                            <?php if (isset($errors['duration'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['duration']); ?></div>
                            <?php endif; ?>
                            <?php if (isset($errors['duration_years'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['duration_years']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Programme</button>
                            <a href="/student-course-hub/admin/programmes" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                        </div>
                    </form>
                <?php endif; ?>
            </main>
            <?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
        </div>
    </div>
</body>
</html>
