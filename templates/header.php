<?php
session_start();
$is_index = strpos($_SERVER['REQUEST_URI'], 'index.php') !== false;
$is_admin = strpos($_SERVER['REQUEST_URI'], 'admin/') !== false;
$is_auth = strpos($_SERVER['REQUEST_URI'], 'auth/') !== false;

// Calculează calea relativă corectă către root
$path_to_root = '';
if ($is_admin || $is_auth) {
    $path_to_root = '../';
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gara Feroviară</title>
    <link rel="stylesheet" href="<?= $path_to_root ?>assets/css/style.css">
</head>
<body>
<header>
    <div class="logo">🚂 Gara Feroviară</div>
    <nav>
        <ul>
            <li><a href="<?= $path_to_root ?>public/index.php" <?= $is_index ? 'class="current-page"' : '' ?>>Acasă</a></li>
            <li><a href="<?= $path_to_root ?>public/orar.php">Orar</a></li>
            <li><a href="<?= $path_to_root ?>public/rute.php">Rute</a></li>
            <li><a href="<?= $path_to_root ?>public/bilete.php">Bilete</a></li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="<?= $path_to_root ?>auth/logout.php">Logout (<?= htmlspecialchars($_SESSION['nume']) ?>)</a></li>
                <?php if ($_SESSION['rol'] === 'administrator'): ?>
                    <li><a href="<?= $path_to_root ?>admin/dashboard.php">Admin</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="<?= $path_to_root ?>auth/login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main>