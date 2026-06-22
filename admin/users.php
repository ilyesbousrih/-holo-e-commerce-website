<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$message = '';
$error = '';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrf)) {
        $error = 'Invalid CSRF token.';
    } else {
        $user_id = (int)$_POST['delete_user'];
        // Don't allow deleting yourself
        if ($user_id == $_SESSION['user_id']) {
            $error = 'You cannot delete your own account!';
        } else {
            if (deleteUser($user_id)) {
                $message = 'User deleted successfully!';
            } else {
                $error = 'Failed to delete user.';
            }
        }
    }
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $csrf = $_POST['csrf_token'] ?? '';
        if (!verifyCsrfToken($csrf)) {
            $error = 'Invalid CSRF token.';
        } elseif ($_POST['action'] === 'add') {
            if (empty($_POST['password'])) {
                $error = 'Password is required for new users.';
            } else {
                $result = adminCreateUser($_POST);
                if ($result['success']) {
                    $message = 'User created successfully!';
                } else {
                    $error = $result['message'];
                }
            }
        } elseif ($_POST['action'] === 'edit' && isset($_POST['user_id'])) {
            if (adminUpdateUser($_POST['user_id'], $_POST)) {
                $message = 'User updated successfully!';
            } else {
                $error = 'Failed to update user.';
            }
        }
    }
}

$users = getAllUsers();
$edit_user = null;

if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_user = getUserById($edit_id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin - Holo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-layout { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: #1a1a2e; color: white; position: fixed; height: 100vh; overflow-y: auto; }
        .admin-logo { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-logo a { color: white; font-size: 1.5rem; font-weight: 700; text-decoration: none; }
        .admin-nav { padding: 1rem 0; }
        .admin-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; color: rgba(255,255,255,0.7); text-decoration: none; transition: all 0.2s; }
        .admin-nav a:hover, .admin-nav a.active { background: rgba(255,255,255,0.1); color: white; }
        .admin-main { flex: 1; margin-left: 260px; background: #f8fafc; }
        .admin-header { background: white; padding: 1rem 2rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; }
        .admin-content { padding: 2rem; }
        .admin-section { background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .btn { padding: 0.625rem 1rem; border-radius: 8px; font-weight: 500; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
        .btn-primary { background: #667eea; color: white; }
        .btn-danger { background: #dc2626; color: white; }
        .btn-secondary { background: #e5e7eb; color: #374151; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 0.875rem; border-bottom: 1px solid #e5e7eb; }
        th { font-weight: 600; color: #6b7280; font-size: 0.875rem; text-transform: uppercase; }
        .role-badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 500; }
        .role-admin { background: #dbeafe; color: #1e40af; }
        .role-customer { background: #d1fae5; color: #065f46; }
        .alert { padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .alert-success { background: #d1fae5; color: #059669; }
        .alert-error { background: #fee2e2; color: #dc2626; }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.show { display: flex; }
        .modal { background: white; border-radius: 12px; padding: 2rem; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input, .form-group select { width: 100%; padding: 0.625rem; border: 1px solid #e5e7eb; border-radius: 6px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .user-avatar { width: 40px; height: 40px; background: #667eea; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; }
        .logout-btn { padding: 0.5rem 1rem; background: rgba(255,255,255,0.1); color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 0.875rem; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-logo"><a href="../index.php">◯ Holo Admin</a></div>
            <nav class="admin-nav">
                <a href="dashboard.php"><span>📊</span> Dashboard</a>
                <a href="products.php"><span>📦</span> Products</a>
                <a href="orders.php"><span>🛒</span> Orders</a>
                <a href="users.php" class="active"><span>👥</span> Users</a>
                <a href="../index.php"><span>🏪</span> Store</a>
            </nav>
            <div style="padding: 1rem 1.5rem; margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1);">
                <p style="font-size: 0.875rem; color: rgba(255,255,255,0.5);">Logged in as</p>
                <p style="font-weight: 500;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                <a href="../logout.php" class="logout-btn" style="display: inline-block; margin-top: 0.75rem;">Sign Out</a>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <h1>User Management</h1>
                <button class="btn btn-primary" onclick="openModal()">+ Add User</button>
            </header>

            <div class="admin-content">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="admin-section">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="user-avatar"><?php echo strtoupper(substr($user['first_name'], 0, 1)); ?></div>
                                        <span><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="?edit=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Delete this user?');">
                                        <input type="hidden" name="delete_user" value="<?php echo $user['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit User Modal -->
    <div class="modal-overlay" id="userModal">
        <div class="modal">
            <h2 style="margin-bottom: 1.5rem;"><?php echo $edit_user ? 'Edit User' : 'Add New User'; ?></h2>
            <form method="POST" action="users.php">
                <input type="hidden" name="action" value="<?php echo $edit_user ? 'edit' : 'add'; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                <?php if ($edit_user): ?>
                <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" value="<?php echo $edit_user ? htmlspecialchars($edit_user['first_name']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" value="<?php echo $edit_user ? htmlspecialchars($edit_user['last_name']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $edit_user ? htmlspecialchars($edit_user['email']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label>Password <?php echo $edit_user ? '(Leave empty to keep current)' : ''; ?></label>
                    <input type="password" name="password" <?php echo $edit_user ? '' : 'required'; ?>>
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="customer" <?php echo ($edit_user && $edit_user['role'] == 'customer') ? 'selected' : ''; ?>>Customer</option>
                        <option value="admin" <?php echo ($edit_user && $edit_user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_user ? 'Update' : 'Add'; ?> User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('userModal').classList.add('show');
        }
        function closeModal() {
            document.getElementById('userModal').classList.remove('show');
        }
        <?php if ($edit_user): ?>
        openModal();
        <?php endif; ?>
    </script>
</body>
</html>
