<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $result = registerUser($email, $password, $first_name, $last_name);
        if ($result['success']) {
            header('Location: login.php?registered=1');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Holo</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .auth-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-logo a {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            text-decoration: none;
        }
        .auth-title {
            font-size: 1.75rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 0.5rem;
            color: #1a1a2e;
        }
        .auth-subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .btn-auth {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #6b7280;
        }
        .auth-footer a {
            color: #667eea;
            font-weight: 500;
            text-decoration: none;
        }
        .auth-footer a:hover {
            text-decoration: underline;
        }
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <a href="index.php">◯ Holo</a>
            </div>
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Join Holo today</p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" placeholder="First name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Last name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Email address" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Min. 6 characters" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                </div>
                <button type="submit" class="btn-auth">Create Account</button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </div>
</body>
</html>
