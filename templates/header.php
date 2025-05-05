<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Preluare setări gara
$settings = [
    'nume_gara' => 'Gara Feroviară', 
    'adresa_gara' => 'Adresă necunoscută',
    'telefon_gara' => '+40000000000',
    'email_gara' => 'contact@example.com'
];
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : $settings['nume_gara']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $_SERVER['REQUEST_URI'] === '../public/index.php' ? '/assets/css/style.css' : '../assets/css/style.css'; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><?php echo htmlspecialchars($settings['nume_gara']); ?></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../public/index.php"><i class="fas fa-home"></i> Acasă</a></li>
                    <li><a href="../public/orar.php"><i class="fas fa-clock"></i> Orar</a></li>
                    <li><a href="../public/rute.php"><i class="fas fa-route"></i> Rute</a></li>
                    <li><a href="../public/bilete.php"><i class="fas fa-ticket-alt"></i> Bilete</a></li>
                    <li><a href="../public/servicii.php"><i class="fas fa-concierge-bell"></i> Servicii</a></li>
                    <li><a href="../public/contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../auth/logout.php" class="btn"><i class="fas fa-sign-out-alt"></i> Deconectare</a>
                    <?php if ($_SESSION['rol'] === 'administrator'): ?>
                        <a href="../admin/dashboard.php" class="btn admin-btn"><i class="fas fa-cog"></i> Admin</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="../auth/login.php" class="btn"><i class="fas fa-sign-in-alt"></i> Autentificare</a>
                    <a href="../auth/register.php" class="btn"><i class="fas fa-user-plus"></i> Înregistrare</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="container">