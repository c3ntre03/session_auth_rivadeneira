<?php
require_once __DIR__ . '/db.php';
preventPageCaching();


if (isset($_SESSION['user_id'])) {
    redirectTo('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';


    if ($identifier === '' || $password === '') {
        $error = 'Please enter your username or email and password.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id, username, email, password_hash
             FROM users
             WHERE username = :username OR email = :email
             LIMIT 1'
        );
        $stmt->execute([
            'username' => $identifier,
            'email' => $identifier
        ]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Incorrect username/email or password.';
        } else {
            session_regenerate_id(true);


            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];


            redirectTo('dashboard.php');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background: #f3f6f4;
            color: #202923;
            font-family: Arial, sans-serif;
        }
        .card {
            width: 100%;
            max-width: 410px;
            padding: 34px;
            background: white;
            border: 1px solid #e4eae6;
            border-radius: 14px;
            box-shadow: 0 12px 35px rgba(25,55,37,.08);
        }
        h1 {
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: #718078;
            font-size: 14px;
        }
        label {
            display: block;
            margin: 17px 0 7px;
            font-size: 14px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #ccd7d0;
            border-radius: 8px;
            font-size: 15px;
        }
        button {
            width: 100%;
            margin-top: 23px;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #198754;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #146c43;
        }
        .message {
            padding: 12px;
            margin-bottom: 16px;
            border-radius: 8px;
            background: #fdeaea;
            color: #a12626;
            font-size: 14px;
        }
        .switch {
            margin-top: 22px;
            text-align: center;
            color: #718078;
            font-size: 14px;
        }
        a {
            color: #198754;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>

<body>

<main class="card">
    <h1>Welcome Back</h1>
    <p class="subtitle">Sign in to your account</p>
    <?php if ($error !== ''): ?>
        <div class="message">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="index.php">
        <label for="identifier">Username or Email</label>
        <input
            type="text"
            id="identifier"
            name="identifier"
            placeholder="Enter username or email"
            value="<?= htmlspecialchars($_POST['identifier'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            required
        >
        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
        >
        <button type="submit">Login</button>
    </form>
    <p class="switch">
        Don't have an account?
        <a href="register.php">Register here</a>
    </p>

</main>


</body>
</html>