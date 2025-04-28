<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nume = $_POST['nume'];
    $email = $_POST['email'];
    $parola = password_hash($_POST['parola'], PASSWORD_DEFAULT);
    $telefon = $_POST['telefon'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (nume, email, parola, telefon) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nume, $email, $parola, $telefon]);
        header("Location: login.php?success=1");
    } catch (PDOException $e) {
        $error = "Eroare la înregistrare: " . $e->getMessage();
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