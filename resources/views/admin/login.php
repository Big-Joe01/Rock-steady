<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('/assets/css/main.css') ?>">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--color-primary-black);
        }
        .login-box {
            width: 100%;
            max-width: 400px;
            padding: var(--space-2xl);
            background: var(--color-dark-gray);
            border-radius: var(--radius-lg);
        }
        .login-logo {
            font-family: var(--font-heading);
            font-size: 2rem;
            text-align: center;
            margin-bottom: var(--space-xl);
            letter-spacing: 0.1em;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">ROCK STEADY</div>
        <h1 class="text-center mb-xl" style="font-family: var(--font-heading); font-size: 1.5rem; letter-spacing: 0.1em;">Admin Access</h1>
        
        <?php if ($error = flash('error')): ?>
        <div class="alert alert-error mb-lg"><?= e($error) ?></div>
        <?php endif; ?>
        
        <form action="/admin/authenticate" method="POST">
                <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" placeholder="Enter admin password" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary w-full">Access Dashboard</button>
        </form>
    </div>
</body>
</html>
<?php
echo ob_get_clean();
