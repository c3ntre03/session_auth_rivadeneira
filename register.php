<?php
require_once __DIR__ . '/db.php';
preventPageCaching();

if (isset($_SESSION['user_id'])) {
    redirectTo('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        $check = $pdo->prepare('SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1');
        $check->execute(['username' => $username, 'email' => $email]);

        if ($check->fetch()) {
            $error = 'That username or email is already registered.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare(
                'INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)'
            );

            try {
                $insert->execute([
                    'username' => $username,
                    'email' => $email,
                    'password_hash' => $passwordHash
                ]);
                $success = 'Account created successfully! You can now log in.';
            } catch (PDOException $e) {
                // Also handles a duplicate submitted at nearly the same time.
                $error = 'Could not create the account. Check whether the username or email is already used.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f4f7f5; font-family: Arial, sans-serif; color: #202923; }
        .card { width: 100%; max-width: 390px; padding: 32px; background: #fff; border: 1px solid #e5ebe7; border-radius: 12px; box-shadow: 0 8px 28px rgba(20, 50, 32, .07); }
        h1 { margin: 0 0 8px; text-align: center; font-size: 27px; }
        .subtitle { margin: 0 0 26px; text-align: center; color: #68756d; font-size: 14px; }
        label { display: block; margin: 16px 0 7px; font-size: 14px; font-weight: 700; }
        input { width: 100%; padding: 12px; border: 1px solid #ccd6cf; border-radius: 7px; font-size: 15px; }
        input:focus { outline: 2px solid #b7e4c7; border-color: #23834b; }
        button { width: 100%; margin-top: 22px; padding: 12px; border: 0; border-radius: 7px; background: #198754; color: white; font-size: 16px; font-weight: 700; cursor: pointer; }
        button:hover { background: #146c43; }
        .message { padding: 11px; margin-bottom: 16px; border-radius: 7px; font-size: 14px; }
        .error { background: #fde8e8; color: #a12626; }
        .success { background: #e4f5e9; color: #176b39; }
        .switch { margin: 20px 0 0; text-align: center; font-size: 14px; color: #68756d; }
        a { color: #198754; font-weight: 700; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <main class="card">
        <h1>Create Account</h1>
        <p class="subtitle">Sign up to get started</p>

        <?php if ($error !== ''): ?>
            <div class="message error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($success !== ''): ?>
            <div class="message success" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" placeholder="Enter username" value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label for="email">Email Address</label>
            <input id="email" name="email" type="email" placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="Create a password (8+ characters)" minlength="8" required>

            <button type="submit">Register</button>
        </form>

        <p class="switch">Already have an account? <a href="index.php">Login here</a></p>
    </main>
</body>
</html>
