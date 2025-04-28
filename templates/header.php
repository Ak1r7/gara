<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_file = basename($_SERVER['PHP_SELF']);
$is_public = strpos($_SERVER['REQUEST_URI'], 'public/') !== false;
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gara Feroviară</title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
</head>
<body>
<header>
    <div class="logo">🚂 Gara Feroviară</div>
    <nav>
        <ul>
            <!-- Linkuri publice -->
            <li><a href="<?= $is_public ? 'index.php' : '../public/index.php' ?>" <?= $current_file === 'index.php' ? 'class="active"' : '' ?>>Acasă</a></li>
            <li><a href="<?= $is_public ? 'orar.php' : '../public/orar.php' ?>" <?= $current_file === 'orar.php' ? 'class="active"' : '' ?>>Orar</a></li>
            <li><a href="<?= $is_public ? 'rute.php' : '../public/rute.php' ?>" <?= $current_file === 'rute.php' ? 'class="active"' : '' ?>>Rute</a></li>
            <li><a href="<?= $is_public ? 'bilete.php' : '../public/bilete.php' ?>" <?= $current_file === 'bilete.php' ? 'class="active"' : '' ?>>Bilete</a></li>
            
            <!-- Linkuri condiționate -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="<?= $is_public ? '../auth/logout.php' : 'auth/logout.php' ?>">Logout (<?= htmlspecialchars($_SESSION['nume']) ?>)</a></li>
                <?php if ($_SESSION['rol'] === 'administrator'): ?>
                    <li><a href="<?= $is_public ? '../admin/dashboard.php' : 'admin/dashboard.php' ?>">Admin</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="<?= $is_public ? '../auth/login.php' : 'auth/login.php' ?>">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main>