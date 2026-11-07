<?php
require_once __DIR__ . '/db.php';
preventPageCaching();

if (!isset($_SESSION['user_id'])) {
    redirectTo('index.php');
}

$stmt = $pdo->prepare('SELECT id, username FROM users WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION = [];
    session_destroy();
    redirectTo('index.php');
}

$_SESSION['username'] = $user['username'];
$currentSessionId = session_id();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f4f7f5; font-family: Arial, sans-serif; color: #202923; }
        .card { width: 100%; max-width: 560px; padding: 34px; background: #fff; border: 1px solid #e5ebe7; border-radius: 12px; box-shadow: 0 8px 28px rgba(20, 50, 32, .07); }
        h1 { margin: 0 0 8px; font-size: 28px; }
        .subtitle { margin: 0 0 26px; color: #68756d; }
        .detail { padding: 14px; margin: 12px 0; border: 1px solid #e5ebe7; border-radius: 8px; overflow-wrap: anywhere; }
        .detail strong { display: block; margin-bottom: 6px; font-size: 13px; color: #68756d; }
        .logout { display: inline-block; margin-top: 18px; padding: 12px 20px; border-radius: 7px; background: #198754; color: #fff; font-weight: 700; text-decoration: none; }
        .logout:hover { background: #146c43; }
    </style>
</head>
<body>
    <main class="card">
        <h1>Welcome, <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>!</h1>
        <p class="subtitle">You are successfully logged in.</p>

        <div class="detail">
            <strong>User ID</strong>
            <?= (int) $user['id'] ?>
        </div>
        <div class="detail">
            <strong>Generated Session ID</strong>
            <?= htmlspecialchars($currentSessionId, ENT_QUOTES, 'UTF-8') ?>
        </div>

        <a class="logout" href="logout.php">Logout</a>
    </main>

    <script>
        
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>
