<?php
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->getConnection();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $parola = $_POST['parola'];
    
    // Validare
    if (empty($email)) {
        $errors['email'] = 'Emailul este obligatoriu';
    }
    
    if (empty($parola)) {
        $errors['parola'] = 'Parola este obligatorie';
    }
    
    if (empty($errors)) {
        // Verificare credentiale
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($parola, $user['parola'])) {
            // Autentificare reușită
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nume'] = $user['nume'];
            $_SESSION['rol'] = $user['rol'];
            
            // Log autentificare
            $ip = $_SERVER['REMOTE_ADDR'];
            $stmt = $conn->prepare("INSERT INTO authentication_logs (user_id, actiune, ip_address) VALUES (?, 'Autentificare', ?)");
            $stmt->execute([$user['user_id'], $ip]);
            
            // Redirect către pagina principală
            header("Location: ../public/index.php");
            exit;
        } else {
            $errors['general'] = 'Email sau parolă incorecte';
        }
    }
}

$page_title = "Autentificare - Gara Feroviară";
require_once __DIR__ . '/../templates/header.php';
?>

<section class="auth-form">
    <div class="container">
        <h1><i class="fas fa-sign-in-alt"></i> Autentificare</h1>
        
        <?php if (isset($errors['general'])): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i>
                <p><?php echo htmlspecialchars($errors['general']); ?></p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group <?php echo isset($errors['email']) ? 'error' : ''; ?>">
                <label for="email">Adresă email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <span class="error-message"><?php echo htmlspecialchars($errors['email']); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group <?php echo isset($errors['parola']) ? 'error' : ''; ?>">
                <label for="parola">Parolă</label>
                <input type="password" name="parola" id="parola" required>
                <?php if (isset($errors['parola'])): ?>
                    <span class="error-message"><?php echo htmlspecialchars($errors['parola']); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-footer">
                <a href="../auth/reset-password.php">Ai uitat parola?</a>
            </div>
            
            <button type="submit" class="btn primary"><i class="fas fa-sign-in-alt"></i> Autentifică-te</button>
            
            <div class="form-footer">
                <p>Nu ai un cont? <a href="../auth/register.php">Înregistrează-te aici</a></p>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>