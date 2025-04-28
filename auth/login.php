<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $parola = $_POST['parola'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($parola, $user['parola'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['nume'] = $user['nume'];
        $_SESSION['rol'] = $user['rol'];
        header("Location: ../public/index.php");
    } else {
        $error = "Email sau parolă incorectă!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare - Gara Feroviară</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../templates/header.php'; ?>

<main class="auth-form">
    <h2>Autentificare</h2>
    <?php if (isset($_GET['success'])): ?>
        <div class="success">Înregistrare reușită! Te poți autentifica acum.</div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="parola" placeholder="Parolă" required>
        <button type="submit">Autentifică-te</button>
    </form>
    <p>Nu ai cont? <a href="register.php">Înregistrează-te aici</a></p>
</main>

<?php include '../templates/footer.php'; ?>
</body>
</html>