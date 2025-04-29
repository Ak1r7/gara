<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nume = filter_input(INPUT_POST, 'nume', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $parola = password_hash($_POST['parola'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (nume, email, parola) VALUES (?, ?, ?)");
        $stmt->execute([$nume, $email, $parola]);
        
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['nume'] = $nume;
        header("Location: /gara/public/index.php");
    } catch (PDOException $e) {
        $error = "Eroare înregistrare: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare - Gara Feroviară</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../templates/header.php'; ?>

<main class="auth-form">
    <h2>Crează un cont nou</h2>
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="nume" placeholder="Nume complet" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="parola" placeholder="Parolă" required>
        <input type="tel" name="telefon" placeholder="Telefon">
        <button type="submit">Înregistrează-te</button>
    </form>
    <p>Ai deja cont? <a href="login.php">Autentifică-te aici</a></p>
</main>

<?php include '../templates/footer.php'; ?>
</body>
</html>