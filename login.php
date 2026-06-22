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
$return_url = 'index.php';
if (!empty($_GET['return_url']) && strpos($_GET['return_url'], 'checkout.php') !== false) {
    $return_url = 'checkout.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $return_url = !empty($_POST['return_url']) && strpos($_POST['return_url'], 'checkout.php') !== false ? 'checkout.php' : 'index.php';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $result = loginUser($email, $password);
        if ($result['success']) {
            // Redirect based on role
            if ($result['user']['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: ' . $return_url);
            }
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
    <title>Login - Holo</title>
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
        .alert-success {
            background: #d1fae5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .demo-accounts {
            margin-top: 1.5rem;
            padding: 1rem;
            background: #f3f4f6;
            border-radius: 8px;
            font-size: 0.875rem;
        }
        .demo-accounts h4 {
            margin: 0 0 0.5rem 0;
            color: #374151;
        }
        .demo-accounts p {
            margin: 0.25rem 0;
            color: #6b7280;
        }
        .demo-accounts code {
            background: #e5e7eb;
            padding: 0.125rem 0.375rem;
            border-radius: 4px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <a href="index.php">◯ Holo</a>
            </div>
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Sign in to your account</p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success">Account created successfully! Please sign in.</div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($return_url, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Email address" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn-auth">Sign In</button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Sign up</a></p>
            </div>
        </div>
    </div>
</body>
</html>
