<?php
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->getConnection();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nume = trim($_POST['nume']);
    $email = trim($_POST['email']);
    $parola = $_POST['parola'];
    $confirma_parola = $_POST['confirma_parola'];
    $telefon = trim($_POST['telefon'] ?? '');
    
    // Validare
    if (empty($nume)) {
        $errors['nume'] = 'Numele este obligatoriu';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Emailul este obligatoriu';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Emailul nu este valid';
    } else {
        // Verificare dacă emailul există deja
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Acest email este deja înregistrat';
        }
    }
    
    if (empty($parola)) {
        $errors['parola'] = 'Parola este obligatorie';
    } elseif (strlen($parola) < 8) {
        $errors['parola'] = 'Parola trebuie să aibă minim 8 caractere';
    }
    
    if ($parola !== $confirma_parola) {
        $errors['confirma_parola'] = 'Parolele nu coincid';
    }
    
    if (empty($errors)) {
        // Înregistrare utilizator
        $hashed_password = password_hash($parola, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("INSERT INTO users (nume, email, parola, telefon) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$nume, $email, $hashed_password, $telefon])) {
            $success = true;
            
            // Autentificare automată după înregistrare
            $user_id = $conn->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            $_SESSION['nume'] = $nume;
            $_SESSION['rol'] = 'utilizator';
            
            // Redirect către pagina principală după 3 secunde
            header("Refresh:3; url=/public/index.php");
        } else {
            $errors['general'] = 'A apărut o eroare la înregistrare. Vă rugăm să încercați din nou.';
        }
    }
}

$page_title = "Înregistrare - Gara Feroviară";
require_once __DIR__ . '/../templates/header.php';
?>

<section class="auth-form">
    <div class="container">
        <h1><i class="fas fa-user-plus"></i> Creare cont nou</h1>
        
        <?php if ($success): ?>
            <div class="alert success">
                <i class="fas fa-check-circle"></i>
                <p>Contul a fost creat cu succes! Veți fi redirecționat în câteva momente.</p>
            </div>
        <?php elseif (!empty($errors['general'])): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i>
                <p><?php echo htmlspecialchars($errors['general']); ?></p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group <?php echo isset($errors['nume']) ? 'error' : ''; ?>">
                <label for="nume">Nume complet</label>
                <input type="text" name="nume" id="nume" value="<?php echo htmlspecialchars($_POST['nume'] ?? ''); ?>" required>
                <?php if (isset($errors['nume'])): ?>
                    <span class="error-message"><?php echo htmlspecialchars($errors['nume']); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group <?php echo isset($errors['email']) ? 'error' : ''; ?>">
                <label for="email">Adresă email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <span class="error-message"><?php echo htmlspecialchars($errors['email']); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group <?php echo isset($errors['telefon']) ? 'error' : ''; ?>">
                <label for="telefon">Număr de telefon (opțional)</label>
                <input type="tel" name="telefon" id="telefon" value="<?php echo htmlspecialchars($_POST['telefon'] ?? ''); ?>">
            </div>
            
            <div class="form-group <?php echo isset($errors['parola']) ? 'error' : ''; ?>">
                <label for="parola">Parolă</label>
                <input type="password" name="parola" id="parola" required>
                <?php if (isset($errors['parola'])): ?>
                    <span class="error-message"><?php echo htmlspecialchars($errors['parola']); ?></span>
                <?php endif; ?>
                <small>Minim 8 caractere</small>
            </div>
            
            <div class="form-group <?php echo isset($errors['confirma_parola']) ? 'error' : ''; ?>">
                <label for="confirma_parola">Confirmă parola</label>
                <input type="password" name="confirma_parola" id="confirma_parola" required>
                <?php if (isset($errors['confirma_parola'])): ?>
                    <span class="error-message"><?php echo htmlspecialchars($errors['confirma_parola']); ?></span>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn primary"><i class="fas fa-user-plus"></i> Înregistrează-te</button>
            
            <div class="form-footer">
                <p>Ai deja un cont? <a href="../auth/login.php">Autentifică-te aici</a></p>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>